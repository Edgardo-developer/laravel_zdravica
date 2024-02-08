<?php

namespace App\Http\Controllers\LeadLinks;

use App\Http\Controllers\Controller;
use App\Models\AmocrmIDs;
use GuzzleHttp\Psr7\Response;

class LeadLinksController extends Controller
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

    public function prepare(array $leadDB, int $AmoBillID = 0){
        return $this->LeadLinksPrepareController->prepare($leadDB,$AmoBillID);
    }

    public function prepareAll($ids){
        return $this->LeadLinksPrepareController->prepareAll($ids);
    }

    public function create($preparedData,$amoLeadID) : Response|array{
        return $this->LeadLinksRequestController->create($this->client, $preparedData,$amoLeadID);
    }

    public function update($preparedData, $amoLeadID) : Response|array{
        return $this->LeadLinksRequestController->update($this->client, $preparedData, $amoLeadID);
    }

    public function remove($preparedData, $amoLeadID) : Response|array{
        return $this->LeadLinksRequestController->remove($this->client, $preparedData, $amoLeadID);
    }
}
