<?php

namespace App\Console\Commands\Leads;

use App\Http\Controllers\Sends\DeleteLeadController;
use App\Jobs\ProcessBulkLead;
use App\Models\AmocrmIDs;
use Illuminate\Console\Command;

class deleteLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:deleteLead
    {--leadDbId=null}
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
            'leadDbId' => $this->option('id'),
            'withreason'   => $this->option('withreason'),
        ];
        if ($options['leadDbId'] !== 'null'){
            $amoLeadID = AmocrmIDs::where('leadDbId', $options['leadDbId'])->first()->amoLeadID;
            if ($amoLeadID){
//                dispatch(new ProcessBulkLead([$amoLeadID],$options['withreason']));
                $withreason = filter_var($options['withreason'], FILTER_VALIDATE_BOOLEAN);
                $DeleteLeads = new DeleteLeadController([$amoLeadID]);
                $DeleteLeads->deleteLeads($withreason);
            }
        }
    }
}
