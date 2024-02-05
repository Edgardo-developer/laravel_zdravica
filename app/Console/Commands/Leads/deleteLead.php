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
    {--leadDBId=null}
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
            'leadDBId' => $this->option('leadDBId'),
            'withReason'   => $this->option('withReason'),
        ];
        if ($options['leadDBId'] !== 'null'){
            Log::info('LeadDBID: '.$options['leadDBId'] . ' DELETE');
            $amoLeadID = AmocrmIDs::where('leadDBId', $options['leadDBId'])->get();
            Log::info('$amoLeadID count is: '.count($amoLeadID));
            Log::info('leadDBId is: '.$options['leadDBId']);
            if (count($amoLeadID) > 0){
                $amoLeadIDFirst = $amoLeadID->first()->amoLeadID;
                Log::info('amoLeadID is: '.$amoLeadIDFirst);
                dispatch(new ProcessBulkLead([$amoLeadIDFirst],$options['withReason']));
            }
        }
    }
}
