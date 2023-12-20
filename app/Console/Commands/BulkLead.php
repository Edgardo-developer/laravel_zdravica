<?php

namespace App\Console\Commands;

use App\Http\Controllers\Leads\LeadRequestController;
use App\Http\Controllers\SendToAmoCRM;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class BulkLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:bulkLead
    {--amoLeadIDs=null}
    {--finish=false}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete the deal from the AmoCRM';

    /**
     * Execute the console command.
     */
    public function handle(SendToAmoCRM $sendDealToAmoCRM)
    {
        $finish = $this->option('finish');
        $amoLeadIDs = $this->option('amoLeadIDs') ? explode(',', $this->option('amoLeadIDs')) : array();
        if ($amoLeadIDs){
            $leadArray = [];
            foreach ($amoLeadIDs as $amoLeadID){
                if ($amoLeadID){
                    $client = new Client(['verify' => false]);
                    $ID = (int)$amoLeadID;
                    if ($ID > 0){
                        $leadArray[] = $finish ?
                            $sendDealToAmoCRM->closeLead($ID) : $sendDealToAmoCRM->finishLead($ID);
                    }
                }
            }
            if (count($leadArray[0]) > 0){
                LeadRequestController::update($client, $leadArray);
            }
        }
    }
}
