<?php

namespace App\Console\Commands\Leads;

use App\Http\Controllers\Sends\DeleteLeadController;
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
    {--withreason=false}
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
    public function handle()
    {
        $options = [
            'leadDBId' => $this->option('leadDBId'),
            'withreason'   => $this->option('withreason'),
        ];
        if ($options['leadDBId'] !== 'null'){
            Log::info('LeadDBID: '.$options['leadDBId'] . ' DELETE');
            $amoLeadID = AmocrmIDs::where('leadDBId', $options['leadDBId'])->first()->amoLeadID;
            if ($amoLeadID){
                dispatch(new ProcessBulkLead([$amoLeadID],$options['withreason']));
            }
        }
    }
}
