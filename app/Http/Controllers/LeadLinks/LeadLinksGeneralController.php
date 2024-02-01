<?php

namespace App\Http\Controllers\LeadLinks;

use App\Http\Controllers\Controller;
use App\Models\AmocrmIDs;

class LeadLinksGeneralController extends Controller
{
    private LeadLinksPrepareController $LeadLinksPrepareController;
    private LeadLinksRequestController $LeadLinksRequestController;

    public function __construct($client){
        $this->LeadLinksPrepareController = new LeadLinksPrepareController();
        $this->LeadLinksRequestController = new LeadLinksRequestController();
        $this->client = $client;
    }

    public function builder($amoLeadID){
        return AmocrmIDs::where('amoLeadID', '=', $amoLeadID)->first()?->amoBillID ?? [];
    }

    public function prepare(array $leadDB, int $AmoBillID){
        return $this->LeadLinksPrepareController->prepare($leadDB,$AmoBillID);
    }

    public function prepareAll($ids){
        return $this->LeadLinksPrepareController->prepareAll($ids);
    }

    public function create($preparedData) : void{
        $this->LeadLinksRequestController->create($this->client, $preparedData);
    }

    public function update($preparedData){
        return $this->LeadLinksRequestController->update($this->client, $preparedData);
    }
}
