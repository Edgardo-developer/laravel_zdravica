<?php

namespace App\Http\Controllers\Sends;

use App\Http\Controllers\Bill\BillController;
use App\Http\Controllers\Contacts\ContactsController;
use App\Http\Controllers\LeadLinks\LeadLinksController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\SendToAmoCRM;
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
        $buildLead = $this->checkAmo($this->buildlead);
        Log::info(print_r($buildLead,true));

        $amoBillID = $this->processBill($buildLead);
        if ($amoBillID && $amoBillID > 0){
            $buildLead['amoBillID']  = $amoBillID;
        }
        $this->updatePatID($buildLead);

        $amoData = $this->prepareDataForAmoCRMIds($buildLead);
        Log::info(print_r($buildLead,true));
        $this->updateLead($buildLead);
        (new \App\Models\AmocrmIDs)->update([
            'leadDBId' => $buildLead['leadDBId']
        ], $amoData);
        return $buildLead;
    }

    /**
     * @param string $offers
     * @return array|array[]
     */
    private static function explodeOffers(string $offers): array
    {
        $arr = [
            'offerNames' => [],
            'offerPrices' => [],
        ];
        $manyOffers = explode('|||', $offers);
        if (count($manyOffers) > 0) {
            foreach ($manyOffers as $singleOffer) {
                $explodeOffer = explode('###', $singleOffer);
                if ($explodeOffer && isset($explodeOffer[0], $explodeOffer[1])) {
                    $arr['offerNames'][] = $explodeOffer[0];
                    $arr['offerPrices'][] = $explodeOffer[1];
                }
            }
        }
        return $arr;
    }

    /**
     * @param $buildLead
     * @param $offersData
     * @return int
     */
    private function getBillAmoID($buildLead, $offersData): int
    {
        $billDB = [
            'offers' => $offersData,
            'price' => (int)$buildLead['billSum'],
            'status' => 'Создан',
            'account' => [
                'entity_type' => 'contacts',
                'entity_id' => (int) $buildLead['amoContactID'],
            ]
        ];
        $amoBillID = $buildLead['amoBillID'] ?? 0;
        if ((!$amoBillID || (int)$amoBillID === 0) && count($offersData['offerNames']) > 0) {
            $amoBillID = $this->BillController->createBill($billDB,0);
        }


        if ($offersData && $offersData['offerNames'] &&
            $buildLead['amoOffers'] !== $buildLead['offerLists'] && $buildLead['amoOffers'] !== null) {
            $this->BillController->updateBill($billDB,'Создан');
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
        if ($buildLead && $buildLead['amoContactID'] && $buildLead['amoLeadID'] && $buildLead['offerLists']) {
            $offersData = self::explodeOffers($buildLead['offerLists']);
            if ($offersData){
                $amoBillID = $this->getBillAmoID($buildLead, $offersData);
                if ($amoBillID){
                    $leadLinks = $this->LeadLinksController->prepare($buildLead, $amoBillID);
                    $this->LeadLinksController->create($leadLinks);
                    $this->ProductController->setProducts($buildLead['amoLeadID'], $offersData);
                }
            }
        }
        return $amoBillID ?? 0;
    }

    /**
     * @param array $dbLead
     * @return array
     */
    protected function checkAmo(array &$dbLead): array
    {
        $raw = AmocrmIDs::where('leadDBId', '=', $dbLead['leadDBId']);
        if (!$raw) {
            return $dbLead;
        }

        AmocrmIDs::where('leadDBId', '=', $dbLead['leadDBId'])->first(['amoContactID', 'amoLeadID', 'amoBillID', 'amoOffers'])->toArray()
        $rawArray = $raw->first(['amoContactID', 'amoLeadID', 'amoBillID', 'amoOffers'])->toArray();
        $dbLead = array_merge(array_diff($dbLead,$rawArray),$rawArray);
//        $keysToCopy = ['amoContactID', 'amoLeadID', 'amoBillID', 'amoOffers'];
//        $rawArray = $raw ? $raw->toArray() : [null, null, null, null];

//        foreach ($keysToCopy as $key) {
//            $dbLead[$key] = isset($raw[$key]) ? $rawArray[$key] : null;
//        }

        ksort($dbLead, SORT_NATURAL);
        return $dbLead;
    }
}
