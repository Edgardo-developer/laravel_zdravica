<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Contacts\ContactsBuilderController;
use App\Http\Controllers\Contacts\ContactsPresendController;
use App\Http\Controllers\Leads\LeadBuilderController;
use App\Http\Controllers\Leads\LeadPrepareController;
use App\Http\Controllers\Leads\LeadPresendController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Models\AmoCrmLead;
use App\Models\AmoCrmTable;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Amp\async;

class CronAmo extends Controller
{
    /**
     * @return void
     * Description: the general method of CRON job
     */
    public function reactOnCron(){
        $lastTimeStamp = AmoCrmTable::all()->where('key', '=', 'timestamp')->first();
        $lastTimeStampVal = $lastTimeStamp?->value;
        if ($lastTimeStampVal){
            $UpdatedLeads = AmoCrmLead::all()
                ->where('updated_at', '>', "1")
                ->where('amoLeadID', '>', 0)
                ->toArray();
            $createdLeads = AmoCrmLead::all()
                ->where('created_at', '>', (integer)$lastTimeStampVal)
                ->whereNull('amoLeadID')
                ->toArray();
            dd($UpdatedLeads, $createdLeads);

            $client = new Client(['verify' => false]);
            if ($UpdatedLeads){
                $this->updateLeads($UpdatedLeads, $client);
            }
            if ($createdLeads){
                $this->createLeads($createdLeads, $client);
            }
        }
        $lastTimeStamp->value = DB::raw('CURRENT_TIMESTAMP');
        $lastTimeStamp->update();
    }

    /**
     * @param array $leadIds
     * @param $client
     * @return void
     * Description: the method works on updating
     */
    private function updateLeads(array $leadIds, $client) : void{
        $sendLeads = [];
        foreach ($leadIds as $leadId){
            $buildLead = $this->prepareLead($leadId['id'], $client);
            $sendLeads[] = LeadPrepareController::prepare($buildLead[0], $buildLead[1]);
        }
        LeadRequestController::update($client, $sendLeads);
    }

    /**
     * @param array $leadIds
     * @param $client
     * @return void
     * Description: the method works on creating
     */
    private function createLeads(array $leadIds, $client){
        $PresendLead = new LeadPresendController();
        foreach ($leadIds as $leadId){
            async(function() use ($PresendLead, $leadId, $client) {
                $preparedLead = $this->prepareLead($leadId, $client);
                $leadRaw = $preparedLead[0];
                $buildLead['amoContactID'] = $preparedLead[1];
                $AmoLeadId = $PresendLead->getAmoID($client, $buildLead);
                $leadRaw->update(['amoLeadID' => $AmoLeadId]);
                $leadRaw->save();
            });
        }
    }

    /**
     * @param int $DBleadId
     * @param $client
     * @return array|void
     * Description: method creates a contact and returns the model of a lead
     */
    private function prepareLead(int $DBleadId, $client){
        $buildLead = LeadBuilderController::getRow($DBleadId);
        $buildContact = ContactsBuilderController::getRow($buildLead['patID']);
        if ($buildLead && $buildContact){
            $leadRaw=AmoCrmLead::find($DBleadId)->first()->toArray();
            $contactAmoId = $buildLead['amoContactID'] ?? $this->getContactAmoID($leadRaw, $client, $buildContact);
            return [$leadRaw, $contactAmoId];
        }
    }

    /**
     * @param $leadRaw
     * @param $client
     * @param $buildContact
     * @return int
     * Description: the method returns the contact ID in the AMO
     */
    private function getContactAmoID($leadRaw, $client, $buildContact){
        $PresendContact = new ContactsPresendController();
        $contactAmoId = $PresendContact->getAmoID($client, $buildContact);
        $leadRaw->update(['amoContactID'  => $contactAmoId]);
        $leadRaw->save();
        return $contactAmoId;
    }
}
