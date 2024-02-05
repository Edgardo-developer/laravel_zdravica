<?php

namespace App\Console\Commands\Leads;

use App\Jobs\ProcessBulkLead;
use App\Models\AmocrmIDs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class deleteLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:deleteLead
    {--amoLeadID=null}
    {--withReason=false}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'the command deletes leads from the AmoCRM';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $options = [
            'amoLeadID' => $this->option('amoLeadID'),
            'withReason'   => $this->option('withReason'),
        ];
        if ($options['amoLeadID'] !== 'null' && (int)$options['amoLeadID'] > 0){
            Log::info('AmoLeadID: '.$options['amoLeadID'] . ' DELETE');
            $amoLeadIDFirst = $options['amoLeadID'];
            dispatch(new ProcessBulkLead([$amoLeadIDFirst],$options['withReason']));
            Log::info('Deleting jobs created');
        }
    }
}
