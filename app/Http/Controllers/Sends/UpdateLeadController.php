<?php

namespace App\Http\Controllers\Sends;

use App\Http\Controllers\Bill\BillController;
use App\Http\Controllers\Contacts\ContactsController;
use App\Http\Controllers\LeadLinks\LeadLinksController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\SendToAmoCRM;
use App\Jobs\CreateLeadJob;
use App\Models\AmocrmIDs;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class UpdateLeadController extends SendToAmoCRM
{
    private array $buildlead;
    private BillController $BillController;
    private LeadLinksController $LeadLinksController;
    private ProductController $ProductController;
    private ContactsController $ContactsController;

    public function __construct($buildlead){
        parent::__construct($buildlead);
        $client = new Client(['verify'=>false]);
        $this->BillController = new BillController($client);
        $this->LeadLinksController = new LeadLinksController($client);
        $this->ProductController = new ProductController($client);
        $this->ContactsController = new ContactsController($client);
        $this->buildlead = $buildlead;
    }

    public function sendDealToAmoCRM() : array{
        Log::info('Updating job for DBLead '.$this->buildlead['leadDBId']);
        $buildLead = $this->checkAmo($this->buildlead);
        if (isset($buildLead['amoContactID'], $buildLead['amoLeadID'], $buildLead['offerLists']) && $buildLead) {

            $offerLists = $this->explodeOffers($buildLead['offerLists']);
            $buildLead['billSum'] = isset($offerLists['offerPrices']) ? array_sum(array_values($offerLists['offerPrices'])) : 0;
            $amoBillID = $this->processBill($buildLead);
            if ($amoBillID && $amoBillID > 0){
                $buildLead['amoBillID']  = $amoBillID;
            }

        }
        $this->updatePatID($buildLead);

        $amoData = $this->prepareDataForAmoCRMIds($buildLead);

        $this->updateLead($buildLead);
        AmocrmIDs::where('leadDBId','=',$buildLead['leadDBId'])
        ->update($amoData);

        return $buildLead;
    }

    public function explodeOffers(string $offers): array
    {
        $arr = [
            'offerNames' => [],
            'offerPrices' => [],
        ];
        $manyOffers = explode('|||', $offers);
        if (count($manyOffers) > 0) {
            foreach ($manyOffers as $singleOffer) {
                $explodeOffer = explode('###', $singleOffer);
                if ($explodeOffer) {
                    if (isset($explodeOffer[0]) && $explodeOffer[0] && $explodeOffer[0] !== ''){
                        $arr['offerNames'][] = $explodeOffer[0];
                    }
                    if (isset($explodeOffer[1]) && $explodeOffer[1] && $explodeOffer[1] !== ''){
                        $arr['offerPrices'][] = $explodeOffer[1];
                    }
                }
            }
        }else if(str_contains('###',$offers)){
            $explodeOffer = explode('###', $offers);
            if ($explodeOffer) {
                if (isset($explodeOffer[0])){
                    $arr['offerNames'][] = $explodeOffer[0];
                }
                if (isset($explodeOffer[1])){
                    $arr['offerPrices'][] = $explodeOffer[1];
                }
            }
        }
        return $arr;
    }

    private function getBillAmoID($buildLead, array $offersData): int
    {
        $amoBillID = $buildLead['amoBillID'] ?? 0;
        $billDB = [
            'offers' => $offersData,
            'price' => $buildLead['billSum'],
            'status' => 'Создан',
            'account' => [
                'entity_type' => 'contacts',
                'entity_id' => (int) $buildLead['amoContactID'],
            ]
        ];

        if ((int)$amoBillID === 0) {
            if (count($offersData['offerNames']) > 0){
                $amoBillID = $this->BillController->createBill($billDB,0);
            }
        }else{
            $billDB['id'] = $amoBillID;
            $res = $this->BillController->updateBill($billDB,0);
            Log::info('The response of updating the Bill was: '.$res->getStatusCode());
        }

        return $amoBillID ?? 0;
    }

    private function updatePatID($buildLead): void
    {
        if (isset($buildLead['patID_changed']) && (bool)$buildLead['patID_changed'] === true){
            $buildContact = $this->getPatData($buildLead);
            $preparedContact = $this->ContactsController->prepare($buildContact);
            $preparedContact['amoID'] = $buildLead['amoContactID'];
            $this->ContactsController->update($preparedContact);
        }
    }

    private function processBill($buildLead) : int{
        $offersData = $this->explodeOffers($buildLead['offerLists']);
        $amoBillID = $this->getBillAmoID($buildLead, $offersData);
        if ($amoBillID && $amoBillID > 0){
            $newOffersData = $this->manageProducts($buildLead);

            $leadLinks = $this->LeadLinksController->prepare($buildLead, $amoBillID);
            $this->LeadLinksController->create($leadLinks,$buildLead['amoLeadID']);

            if (isset($newOffersData['link']['offerNames']) && count($newOffersData['link']['offerNames']) > 0){
                $this->ProductController->setProducts($buildLead['amoLeadID'], $newOffersData['link']);
            }
            if (isset($newOffersData['unlink']['offerNames']) && count($newOffersData['unlink']['offerNames']) > 0){
                $this->ProductController->unsetProducts($buildLead['amoLeadID'], $newOffersData['unlink']);
            }
        }
        return $amoBillID ?? 0;
    }

    public function manageProducts($buildLead) : array{
        $amoOffers = $this->explodeOffers($buildLead['amoOffers']);
        $offersList = $this->explodeOffers($buildLead['offerLists']);

        $link = [];
        $unlink = [];
        if (count($amoOffers['offerNames']) > 0 && count($offersList['offerNames']) > 0){
            // AmoCRM has less products than DB
            // return for update
            if (count($amoOffers['offerNames']) < count($offersList['offerNames'])){
                $link = $this->getDiffOffersLink($amoOffers,$offersList);
            }

            // AmoCRM has more products than DB
            // Unlink
            if (count($amoOffers['offerNames']) > count($offersList['offerNames'])){
                $unlink = $this->getDiffOffersUnlink($amoOffers,$offersList);
            }
        }else{
            if (count($amoOffers['offerNames']) > 0){
                $unlink = $amoOffers;
            }else{
                $link = $offersList;
            }
        }

        return ['link'=>$link,'unlink'=>$unlink];
    }

    private function getDiffOffersUnlink($amoOffers,$offersList) : array{
        // Те, что в амо. Их должно быть больше
        $amoFullOffers = array_combine($amoOffers['offerNames'],$amoOffers['offerPrices']);
        // Те, что в БД. Их должно быть меньше
        $DBFullOffers = array_combine($offersList['offerNames'],$offersList['offerPrices']);

        $result = array_diff_assoc($amoFullOffers, $DBFullOffers);

        return ['offerNames'=>array_keys($result),'offerPrices'=>array_values($result)];
    }

    private function getDiffOffersLink($amoOffers,$offersList) : array{
        // Те, что в амо. Их должно быть меньше
        $amoFullOffers = array_combine($amoOffers['offerNames'],$amoOffers['offerPrices']);
        // Те, что в БД. Их должно быть больше
        $DBFullOffers = array_combine($offersList['offerNames'],$offersList['offerPrices']);

        $result = array_diff_assoc($DBFullOffers, $amoFullOffers);

        return ['offerNames'=>array_keys($result),'offerPrices'=>array_values($result)];
    }

    protected function checkAmo(array &$dbLead): array
    {
        $raw = AmocrmIDs::where('leadDBId', '=', $dbLead['leadDBId']);

        $rawArray = $raw->first(['amoContactID', 'amoLeadID', 'amoBillID', 'amoOffers'])?->toArray();
        if ($rawArray){
            $dbLead = array_merge(array_diff($dbLead,$rawArray),$rawArray);
            ksort($dbLead, SORT_NATURAL);
        }

        return $dbLead;
    }
}
