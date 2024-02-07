<?php

namespace App\Console\Commands\Leads;

use App\Http\Controllers\Sends\DeleteLeadController;
use App\Http\Controllers\SendToAmoCRM;
use App\Jobs\ProcessBulkLead;
use App\Models\AmocrmIDs;
use Illuminate\Console\Command;

class BulkLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:bulkLead
    {--leadDBIds=null}
    {--withreason=false}
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
    public function handle()
    {
        $options = [
            'leadDBIds' => $this->option('leadDBIds'),
            'withReason'   => $this->option('withReason'),
        ];
        if ($options['amoLeadIDs'] !== 'null'){
            $leadDBIds = explode(',',$options['leadDBIds']);
            $amoLeadIDs = AmocrmIDs::whereIn('leadDBId', $leadDBIds)->pluck('amoLeadID')->toArray();
            if ($leadDBIds){
                dispatch(new ProcessBulkLead($amoLeadIDs,$options['withReason']));
            }
        }
    }
}
