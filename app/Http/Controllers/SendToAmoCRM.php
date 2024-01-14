<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Bill\BillPresendController;
use App\Http\Controllers\Contacts\ContactsBuilderController;
use App\Http\Controllers\Contacts\ContactsPresendController;
use App\Http\Controllers\LeadLinks\LeadLinksPrepareController;
use App\Http\Controllers\LeadLinks\LeadLinksRequestController;
use App\Http\Controllers\Leads\LeadPrepareController;
use App\Http\Controllers\Leads\LeadPresendController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Http\Controllers\Product\ProductPresendController;
use App\Models\AmocrmIDs;
use GuzzleHttp\Client;
use JsonException;

class SendToAmoCRM extends Controller
{

    /**
     * @param $DBlead
     * @return void
     */
    public function sendDealToAmoCRM($DBlead): void
    {
        $buildLead = $this->checkAmo($DBlead);
        $client = new Client(['verify' => false]);
        if ($DBlead['delete'] && isset($buildLead['amoLeadID'])){
            self::deleteLead($buildLead);
            return;
        }

        $buildContact = ContactsBuilderController::getRow(
            (int)$buildLead['patID'],
            (int)$buildLead['declareCall'] === 1
        );
        if ($buildLead && $buildContact) {
            $buildLead['amoContactID'] = $this->getContactAmoID($client, $buildLead, $buildContact);
            $buildLead['amoLeadID'] = $this->getLeadAmoID($client, $buildLead);

            if ($buildLead['offerLists'] !== 'null' && $buildLead['offerLists'] !== '') {
                $buildLead['offersData'] = self::explodeOffers($buildLead['offerLists']);
                $buildLead['amoBillID'] = $this->getBillAmoID($client, $buildLead);
                $this->setProducts($client, $buildLead);
            }

            $leadPrepared = LeadPrepareController::prepare($buildLead, $buildLead['amoContactID']);
            $leadPrepared['id'] = $buildLead['amoLeadID'];
            $leadPrepared['pipeline_id'] = 7332486;
            $leadPrepared['status_id'] = 61034286;
            LeadRequestController::update($client, [$leadPrepared]);

            $amoData = [
                'amoContactID' => '',
                'amoLeadID' => '',
                'amoBillID' => '',
                'offers' => '',
                'leadDBId' => ''
            ];

            foreach ($amoData as $k => &$IdsName) {
                if ($buildLead[$k] && $buildLead[$k] !== 'null') {
                    $amoData[$k] = $buildLead[$k];
                } else {
                    unset($amoData[$k]);
                }
            }
            unset($IdsName);
            AmocrmIDs::updateOrCreate([
                'leadDBId' => $buildLead['leadDBId']
            ], $amoData);
        }
    }

    /**
     * @param array $dbLead
     * @return array
     */
    private function checkAmo(array &$dbLead): array
    {
        $raw = AmocrmIDs::all()->where('leadDBId', '=', $dbLead['leadDBId'])?->first();
        $keysToCopy = ['amoContactID', 'amoLeadID', 'amoBillID', 'amoOffers'];
        $rawArray = $raw ? $raw->toArray() : [null, null, null, null];

        foreach ($keysToCopy as $key) {
            $dbLead[$key] = isset($raw[$key]) ? $rawArray[$key] : null;
        }
        ksort($dbLead, SORT_NATURAL);
        return $dbLead;
    }

    /**
     * @param $client
     * @param $buildLead
     * @param $buildContact
     * @return int
     */
    private function getContactAmoID($client, $buildLead, $buildContact): int
    {
        return $buildLead['amoContactID'] ?? (new ContactsPresendController())->getAmoID($client, $buildContact);
    }

    /**
     * @param $client
     * @param $buildLead
     * @return int
     */
    private function getLeadAmoID($client, $buildLead): int
    {
        if (!isset($buildLead['amoLeadID']) || $buildLead['amoLeadID'] === 'null') {
            return (new LeadPresendController())->getAmoID($client, $buildLead);
        }
        return $buildLead['amoLeadID'];
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
     * @param $client
     * @param $buildLead
     * @return int
     */
    private function getBillAmoID($client, $buildLead): int
    {
        $billDB = [
            'offers' => $buildLead['offersData'],
            'price' => $buildLead['billSum'],
            'billStatus' => 0,
            'status' => 'Создан',
            'account' => [
                'entity_type' => 'contacts',
                'entity_id' => $buildLead['amoContactID'],
            ]
        ];
        if ($buildLead['amoBillID'] === null && count($buildLead['offersData']['offerNames']) > 0) {
            $PresendBill = new BillPresendController();
            $AmoBillID = $PresendBill->getAmoID($client, $billDB);
            $leadLinks = LeadLinksPrepareController::prepare($buildLead, $AmoBillID);
            $leadLinks['amoLeadID'] = $buildLead['amoLeadID'];
            LeadLinksRequestController::create($client, $leadLinks);
            return $AmoBillID;
        }


        if ($buildLead['offersData'] && $buildLead['offersData']['offerNames'] &&
            $buildLead['amoOffers'] !== $buildLead['offerLists'] && $buildLead['amoOffers'] !== null) {
            $PresendBill = new BillPresendController();
            $PresendBill->updateBill($client, $billDB);
        }
        return $buildLead['amoBillID'] ?? 0;
    }

    /**
     * @param $client
     * @param $buildLead
     * @return void
     */
    private function setProducts($client, $buildLead)
    {
        if (count($buildLead['offersData']['offerNames']) > 0){
            $ProductPresend = new ProductPresendController();
            $productIDs = $ProductPresend->getAmoIDs($client, $buildLead['offersData']['offerNames']);
            $linksPrepared = LeadLinksPrepareController::prepareAll($productIDs);
            $linksPrepared['amoLeadID'] = $buildLead['amoLeadID'];
            LeadLinksRequestController::update($client, $linksPrepared);
        }
    }
    private static function deleteLead($leadArray){
        $client = new Client(['verify'=>false]);
        LeadRequestController::update($client, [[
            "id"    => $leadArray['amoID'],
            'is_deleted'    => true,
        ]]);
    }
}
