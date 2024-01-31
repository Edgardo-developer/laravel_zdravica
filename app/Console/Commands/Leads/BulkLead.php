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
    {--amoLeadIDs=null}
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
    public function handle(SendToAmoCRM $sendDealToAmoCRM)
    {
        $options = [
            'amoLeadIDs' => $this->option('id'),
            'withreason'   => $this->option('withreason'),
        ];
        if ($options['amoLeadIDs'] !== 'null'){
            $amoLeadIDsArr = explode(',',$options['amoLeadIDs']);
            if ($amoLeadIDsArr){
                $withreason = filter_var($options['withreason'], FILTER_VALIDATE_BOOLEAN);
                $DeleteLeads = new DeleteLeadController($amoLeadIDsArr);
                $DeleteLeads->deleteLeads($withreason);
//                dispatch(new ProcessBulkLead($amoLeadIDsArr,$options['withreason']));
            }
        }
    }
}
