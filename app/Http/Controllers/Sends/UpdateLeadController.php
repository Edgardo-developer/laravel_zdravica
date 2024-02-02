<?php

namespace App\Http\Controllers\Sends;

use App\Http\Controllers\Bill\BillGeneralController;
use App\Http\Controllers\Contacts\ContactsGeneralController;
use App\Http\Controllers\Contacts\ContactsPrepareController;
use App\Http\Controllers\Contacts\ContactsRequestController;
use App\Http\Controllers\LeadLinks\LeadLinksGeneralController;
use App\Http\Controllers\Product\ProductGeneralController;
use App\Http\Controllers\SendToAmoCRM;
use App\Models\AmocrmIDs;
use GuzzleHttp\Client;

class UpdateLeadController extends SendToAmoCRM
{
    private $buildlead;
    private BillGeneralController $BillGeneralController;
    private LeadLinksGeneralController $LeadLinksGeneralController;
    private ProductGeneralController $ProductGeneralController;
    private ContactsGeneralController $ContactsGeneralController;

    public function __construct($buildlead){
        parent::__construct($buildlead);
        $client = new Client(['verify'=>false]);
        $this->BillGeneralController = new BillGeneralController($client);
        $this->LeadLinksGeneralController = new LeadLinksGeneralController($client);
        $this->ProductGeneralController = new ProductGeneralController($client);
        $this->ContactsGeneralController = new ContactsGeneralController($client);
        $this->buildlead = $buildlead;
    }

    public function sendDealToAmoCRM() : array{
        $buildLead = $this->checkAmo($this->buildlead);

        $amoBillID = $this->processBill($buildLead);
        if ($amoBillID){
            $buildLead['amoBillID']  = $amoBillID;
        }
        $this->updatePatID($buildLead);

        $amoData = $this->prepareDataForAmoCRMIds($buildLead);
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
        $manyOffers = explode(',', $offers);
        if (count($manyOffers) > 0) {
            foreach ($manyOffers as $singleOffer) {
                $explodeOffer = explode(':', $singleOffer);
                if ($explodeOffer) {
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
            'price' => $buildLead['billSum'],
            'billStatus' => 0,
            'status' => 'Создан',
            'account' => [
                'entity_type' => 'contacts',
                'entity_id' => (int) $buildLead['amoContactID'],
            ]
        ];
        if ($buildLead['amoBillID'] === null && count($offersData['offerNames']) > 0) {
            return $this->BillGeneralController->getAmoID($billDB);
        }


        if ($offersData && $offersData['offerNames'] &&
            $buildLead['amoOffers'] !== $buildLead['offerLists'] && $buildLead['amoOffers'] !== null) {
            $this->BillGeneralController->updateBill($billDB);
        }
        return $buildLead['amoBillID'] ?? 0;
    }

    /**
     * @param $buildLead
     * @param $offersData
     * @return void
     */
    private function setProducts($buildLead, $offersData): void
    {
        if (count($offersData['offerNames']) > 0){
            $productIDs = $this->ProductGeneralController->getAmoIDs($offersData['offerNames']);
            $linksPrepared = $this->LeadLinksGeneralController->prepareAll($productIDs);
            $linksPrepared['amoLeadID'] = $buildLead['amoLeadID'];
            $this->LeadLinksGeneralController->update($linksPrepared);
        }
    }

    private function updatePatID($buildLead): void
    {
        if (isset($buildLead['patID_changed']) && $buildLead['patID_changed'] === true){
            $buildContact = $this->getPatData($buildLead);
            $preparedContact = $this->ContactsGeneralController->prepare($buildContact);
            $preparedContact['amoID'] = $buildLead['amoContactID'];
            $this->ContactsGeneralController->update($preparedContact);
        }
    }

    private function processBill($buildLead){
        if ($buildLead && $buildLead['amoContactID'] && $buildLead['amoLeadID'] && $buildLead['offerLists']) {
            $offersData = self::explodeOffers($buildLead['offerLists']);
            if ($offersData){
                $amoBillID = $this->getBillAmoID($buildLead, $offersData);
                if ($amoBillID){
                    $leadLinks = $this->LeadLinksGeneralController->prepare($buildLead, $amoBillID);
                    $leadLinks['amoLeadID'] = $buildLead['amoLeadID'];
                    $this->LeadLinksGeneralController->create($leadLinks);
                    $this->setProducts($buildLead, $offersData);
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
        $raw = AmocrmIDs::where('leadDBId', '=', $dbLead['leadDBId'])?->first();
        $keysToCopy = ['amoContactID', 'amoLeadID', 'amoBillID', 'amoOffers'];
        $rawArray = $raw ? $raw->toArray() : [null, null, null, null];

        foreach ($keysToCopy as $key) {
            $dbLead[$key] = isset($raw[$key]) ? $rawArray[$key] : null;
        }

        ksort($dbLead, SORT_NATURAL);
        return $dbLead;
    }
}
