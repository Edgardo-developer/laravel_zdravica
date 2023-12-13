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
use Illuminate\Support\Facades\DB;
use Amp\ReactAdapter\ReactAdapter;

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
        $previousDay = time() - strtotime("-1 days");
        if ($lastTimeStampVal){
            $client = new Client(['verify' => false]);

            $deletedLeads = AmoCrmLead::all('id')
                ->where('created_at', '<', $previousDay)
                ->where('amoLeadID', '>', 0)
                ->where('declareVisit', '=', 0)
                ->toArray();
            if ($deletedLeads){
                $this->deleteLeads($deletedLeads, $client);
            }

            $UpdatedLeads = AmoCrmLead::all()
                ->where('updated_at', '>', (integer)$lastTimeStampVal)
                ->where('amoLeadID', '>', 0)
                ->toArray();
            $createdLeads = AmoCrmLead::all()
                ->where('created_at', '>', (integer)$lastTimeStampVal)
                ->whereNull('amoLeadID')
                ->toArray();

            $LeadBuilderController = new LeadBuilderController();
            $ContactsBuilderController = new ContactsBuilderController();
            if ($UpdatedLeads){
                $this->updateLeads($UpdatedLeads, $client,
                    $LeadBuilderController, $ContactsBuilderController);
            }
            if ($createdLeads){
                $this->createLeads($createdLeads, $client,
                    $LeadBuilderController, $ContactsBuilderController);
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
    private function updateLeads(array $leadIds, $client,
        LeadBuilderController $LeadBuilderController,
        ContactsBuilderController $ContactsBuilderController
        ) : void{
        $sendLeads = [];
        foreach ($leadIds as $leadId){
            $buildLead = $this->prepareLead($leadId['id'], $client, $LeadBuilderController, $ContactsBuilderController);
            if ($buildLead){
                $sendLeads[] = LeadPrepareController::prepare($buildLead[0]->toArray(), $buildLead[1]);
            }
        }
        LeadRequestController::update($client, $sendLeads);
    }

    private function deleteLeads(array $leadIds, $client){
        $sendLeads = [];
        foreach ($leadIds as $leadId){
            $sendLeads[] =  [
                'AmoLeadId' => $leadId['id'],
                "name" => "1",
                "closed_at"=> time() + 5,
                "status_id"=> 143,
                "updated_by"=> 0
            ];
            AmoCrmLead::find($leadId['id'])?->delete();
        }
        LeadRequestController::update($client, $sendLeads);
    }

    /**
     * @param array $leadIds
     * @param $client
     * @return void
     * Description: the method works on creating
     */
    private function createLeads(array $leadIds, $client,
        LeadBuilderController $LeadBuilderController,
        ContactsBuilderController $ContactsBuilderController){
        $PresendLead = new LeadPresendController();
        foreach ($leadIds as $leadId){
            $preparedLead = $this->prepareLead($leadId['id'], $client, $LeadBuilderController, $ContactsBuilderController);
            if ($preparedLead){
                $leadRaw = $preparedLead[0];
                $buildLead = $leadRaw->toArray();
                $buildLead['amoContactID'] = $preparedLead[1];
                $PresendLead->getAmoID($client, $buildLead, $leadRaw);
//                $AmoLeadId = $PresendLead->getAmoID($client, $buildLead, $leadRaw);
//                $leadRaw->update(['amoLeadID' => $AmoLeadId]);
//                $leadRaw->save();
            }
            unset($leadId, $leadRaw);
        }
    }

    /**
     * @param int $DBleadId
     * @param $client
     * @return array
     * Description: method creates a contact and returns the model of a lead
     */
    private function prepareLead(int $DBleadId, $client,
        LeadBuilderController $LeadBuilderController,
        ContactsBuilderController $ContactsBuilderController
    ) : array{
        $buildLead = $LeadBuilderController->getRow($DBleadId);
        $buildContact = $ContactsBuilderController->getRow($buildLead['patID']);
        if ($buildLead && $buildContact){
            $leadRaw= AmoCrmLead::all()->where('id', '=', $DBleadId)->first();
            $contactAmoId = $buildLead['amoContactID'] ?? $this->getContactAmoID($leadRaw, $client, $buildContact);
            return [$leadRaw, $contactAmoId];
        }
        return [];
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
