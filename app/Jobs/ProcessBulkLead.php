<?php

namespace App\Jobs;

use App\Http\Controllers\Sends\DeleteLeadController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        Log::info('This is job of deleting ' . date('H:i:s'));
        $withreason = filter_var($this->withreason, FILTER_VALIDATE_BOOLEAN);
        $DeleteLeads = new DeleteLeadController($this->amoLeadIDs);
        $DeleteLeads->deleteLeads($withreason);
    }
}
