<?php

namespace App\Console\Commands;

use App\Http\Controllers\Bill\BillBuilderController;
use App\Http\Controllers\Bill\BillRequestController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Http\Controllers\SendToAmoCRM;
use App\Models\amocrmIDs;
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
            $billArray = [];
            foreach ($amoLeadIDs as $amoLeadID){
                if ($amoLeadID){
                    $leadID = (int)$amoLeadID;
                    $billID = amocrmIDs::all()->where('amoLeadID', '=', $amoLeadID)->first()->amoBillID;

                    if ($leadID > 0) {
                        $leadArray[] = $finish ?
                            $sendDealToAmoCRM->closeLead($leadID) :
                            $sendDealToAmoCRM->finishLead($leadID);
                    }
                    if ($billID > 0){
                        $billArray[] = $finish ?
                            BillBuilderController::closeBill($billID) :
                            BillBuilderController::finishBill($billID);
                    }

                }
            }
            if (count($leadArray[0]) > 0){
                $client = new Client(['verify' => false]);
                LeadRequestController::update($client, $leadArray);
                BillRequestController::update($client, $billArray);
            }
        }
    }
}
