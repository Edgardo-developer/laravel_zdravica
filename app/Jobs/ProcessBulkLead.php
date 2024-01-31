<?php

namespace App\Jobs;

use App\Http\Controllers\Bill\BillBuilderController;
use App\Http\Controllers\Bill\BillRequestController;
use App\Http\Controllers\Leads\LeadBuilderController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Http\Controllers\Sends\DeleteLeadController;
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
    public function __construct($amoLeadIDs, $withreason)
    {
        $this->withreason = $withreason;
        $this->amoLeadIDs = $amoLeadIDs;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $withreason = filter_var($this->withreason, FILTER_VALIDATE_BOOLEAN);
        $DeleteLeads = new DeleteLeadController($this->amoLeadIDs);
        $DeleteLeads->deleteLeads($withreason);
    }
}
