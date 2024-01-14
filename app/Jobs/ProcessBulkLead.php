<?php

namespace App\Jobs;

use App\Http\Controllers\Bill\BillBuilderController;
use App\Http\Controllers\Bill\BillRequestController;
use App\Http\Controllers\Leads\LeadBuilderController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Models\AmocrmIDs;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBulkLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $amoLeadIDs;
    protected $finish;
    /**
     * Create a new job instance.
     */
    public function __construct($amoLeadIDs, $finish)
    {
        $this->finish = $finish;
        $this->amoLeadIDs = $amoLeadIDs;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $leadArray = [];
        $billArray = [];
        foreach ($this->amoLeadIDs as $amoLeadID) {
            if ($amoLeadID) {
                $leadID = (int)$amoLeadID;
                $billID = amocrmIDs::all()->where('amoLeadID', '=', $amoLeadID)->first()->amoBillID;

                if ($leadID > 0) {
                    $leadArray[] = $this->finish ?
                        LeadBuilderController::closeLead($leadID) :
                        LeadBuilderController::finishLead($leadID);
                }
                if ($billID > 0 && $this->finish) {
                    $billArray[] = BillBuilderController::finishBill($billID);
                }
            }
        }
        if (count($leadArray[0]) > 0) {
            $client = new Client(['verify' => false]);
            if (count($billArray[0]) > 0) {
                BillRequestController::update($client, $billArray);
            }
            LeadRequestController::update($client, $leadArray);
        }
    }
}
