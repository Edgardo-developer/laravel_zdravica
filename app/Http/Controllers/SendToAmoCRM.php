<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Contacts\ContactsController;
use App\Http\Controllers\Leads\LeadController;
use App\Models\AmocrmIDs;
use App\Models\PLANNING;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SendToAmoCRM extends Controller
{
    private ContactsController $ContactsController;
    private LeadController $LeadController;

    public function __construct($DBlead)
    {
        $client = new Client(['verify'=>false]);
        $this->ContactsController = new ContactsController($client);
        $this->LeadController = new LeadController($client);
        $this->DBlead = $DBlead;
    }

    /**
     * @return array
     */
    public function sendDealToAmoCRM(): array
    {
        Log::info('Creating jobs for DBLead '.$this->DBlead['leadDBId']);
        $buildLead = $this->DBlead;
        $buildLead['FIO'] = $this->getPlanningFIO($this->DBlead);
        ksort($buildLead, SORT_NATURAL);

        $buildContact = $this->getPatData($buildLead);
        if (isset($buildContact['NE_LE'])){
            $buildLead['agePat'] = $buildContact['agePat'] = $this->getAge($buildContact['NE_LE']);
        }

        if ($buildLead && $buildContact) {
            $buildLead['amoContactID'] = $this->ContactsController->getAmoID($buildContact);
            $buildLead['amoLeadID'] = $this->LeadController->getAmoID($buildLead);

            $this->updateLead($buildLead);
            $buildLead['amoLeadID'] = (int)$buildLead['amoLeadID'];
            $amoData = $this->prepareDataForAmoCRMIds($buildLead);
            AmocrmIDs::create($amoData);
            return $buildLead;
        }
        return [];
    }

    /**
     * @param array $dbLead
     * @return string
     */
    protected function getPlanningFIO(array &$dbLead): string
    {
        if (isset($dbLead['fioPat']) && $dbLead['fioPat'] !== ''){
            return $dbLead['fioPat'];
        }
        $PLANNING = PLANNING::where('PLANNING_ID','=',$dbLead['leadDBId']);
        if ($PLANNING && $PLANNING->count() > 0) {
            $planningFirst = $PLANNING->first();
            return $planningFirst?->PRENOM . ' ' . $planningFirst->NOM . ' ' . $planningFirst?->PATRONYME;
        }
        return '';
    }

    protected function updateLead(array $buildLead)
    {
        $leadPrepared = $this->LeadController->prepare($buildLead, $buildLead['amoContactID']);
        $leadPrepared['amoLeadID'] = (int)$buildLead['amoLeadID'];
        $leadPrepared['pipeline_id'] = 7332486;
        $leadPrepared['status_id'] = 61034286;
        $this->LeadController->update($leadPrepared);
    }

    protected function prepareDataForAmoCRMIds(array $buildLead)
    {
        $amoData = [
            'amoContactID' => '',
            'amoLeadID' => '',
            'amoBillID' => '',
            'amoOffers' => '',
            'leadDBId' => ''
        ];
        foreach ($amoData as $k => &$IdsName) {
            if ($k === 'amoOffers' && isset($buildLead['offerLists'])) {
                $amoData[$k] = $buildLead['offerLists'];
                continue;
            }
            if (isset($buildLead[$k]) && $buildLead[$k] !== 'null' && $buildLead[$k] && $buildLead[$k] !== '') {
                $amoData[$k] = $buildLead[$k];
            } else {
                unset($amoData[$k]);
            }
        }
        unset($IdsName);
        return $amoData;
    }

    protected function getPatData(array $buildLead)
    {
        if (isset($buildLead['patID']) && (int)$buildLead['patID'] > 0) {
            $buildContact = $this->ContactsController->getRow(
                (int)$buildLead['patID'],
                (int)$buildLead['declareCall'] === 1
            );
            if (isset($buildLead['fioPat'])){
                $buildContact['FIO'] = $buildLead['fioPat'];
            }
        } else {
            $buildContact = ['FIO' => $buildLead['FIO'] ?? ''];
        }
        return $buildContact;
    }

    private function getAge(string $birthday) : int{
        $time = strtotime($birthday);
        $timeDataTime = date('Y',$time);
        $timeDataNow = date('Y');
        return $timeDataNow - $timeDataTime;
    }
}
