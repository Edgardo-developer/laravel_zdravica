<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Contacts\ContactsBuilderController;
use App\Http\Controllers\Contacts\ContactsPresendController;
use App\Http\Controllers\Leads\LeadPrepareController;
use App\Http\Controllers\Leads\LeadPresendController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Models\AmocrmIDs;
use App\Models\PLANNING;
use GuzzleHttp\Client;

class SendToAmoCRM extends Controller
{
    public function __construct($DBlead)
    {
        $this->DBlead = $DBlead;
    }

    /**
     * @param $DBlead
     * @return array
     */
    public function sendDealToAmoCRM(): array
    {
        $buildLead = $this->getPlanningFIO($this->DBlead);
        $client = new Client(['verify' => false]);

        $buildContact = $this->getPatData($buildLead);

        if ($buildLead && $buildContact) {
            $buildLead['amoContactID'] = (new ContactsPresendController())->getAmoID($client, $buildContact);
            $buildLead['amoLeadID'] = (new LeadPresendController())->getAmoID($client, $buildLead);

            $this->updateLead($buildLead, $client);
            $buildLead['amoLeadID'] = (int)$buildLead['amoLeadID'];
            $amoData = $this->prepareDataForAmoCRMIds($buildLead);

            AmocrmIDs::create($amoData);
            return $buildLead;
        }
        return [];
    }

    /**
     * @param array $dbLead
     * @return array
     */
    protected function getPlanningFIO(array &$dbLead): array
    {
        $PLANNING = PLANNING::find($dbLead['leadDBId'], 'PLANNING_ID');
        if ($PLANNING && $PLANNING->count() > 0) {
            $planningFirst = $PLANNING->first();
            $dbLead['FIO'] = $planningFirst->NOM . ' ' . $planningFirst?->PRENOM . ' ' . $planningFirst?->PATRONYME;
        }
        ksort($dbLead, SORT_NATURAL);
        return $dbLead;
    }

    protected function updateLead($buildLead, $client)
    {
        $leadPrepared = LeadPrepareController::prepare($buildLead, $buildLead['amoContactID']);
        $leadPrepared['amoLeadID'] = (int)$buildLead['amoLeadID'];
        $leadPrepared['pipeline_id'] = 7332486;
        $leadPrepared['status_id'] = 61034286;
        LeadRequestController::update($client, $leadPrepared);
    }

    protected function prepareDataForAmoCRMIds($buildLead)
    {
        $amoData = array_intersect_key($buildLead, [
            'amoContactID' => '',
            'amoLeadID' => '',
            'amoBillID' => '',
            'offers' => '',
            'leadDBId' => ''
        ]);
        foreach ($amoData as $k => &$IdsName) {
            if ($buildLead[$k] !== 'null') {
                if ($k === 'offers') {
                    $amoData[$k] = (int)$buildLead[$k];
                } else {
                    $amoData[$k] = $buildLead[$k];
                }
            } else {
                unset($amoData[$k]);
            }
        }
        unset($IdsName);
        return $amoData;
    }

    protected function getPatData($buildLead)
    {
        if ((int)$buildLead['patID'] > 0) {
            $buildContact = ContactsBuilderController::getRow(
                (int)$buildLead['patID'],
                (int)$buildLead['declareCall'] === 1
            );
        } else {
            $buildContact = ['FIO' => $buildLead['FIO'] ?? ''];
        }
        return $buildContact;
    }
}
