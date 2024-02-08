<?php

namespace App\Console\Commands\Leads;

use App\Http\Controllers\SendToAmoCRM;
use App\Jobs\CreateLeadJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class createLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:createLead
    {--id=null}
    {--direction=null}
    {--updated_at=null}
    {--created_at=null}
    {--specDoc=null}
    {--patID=null}
    {--fioDoc=null}
    {--fioPat=null}
    {--declareVisit=null}
    {--declareCall=null}
    {--filial=null}
    {--date=null}
    {--billID=null}
    {--billSum=null}
    {--offers=null}
    {--offerLists=null}
    {--managerName=null}
    {--amoManagerID=null}
    {--leadDBId=null}
    {--amoLeadID=null}
    {--amoContactID=null}
    {--responsibleFIO=null}
    {--responsible_user_id=null}
';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the deal to the AmoCRM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $options = [
            'id' => $this->option('id'),
            'patID' => $this->option('patID'),
            'billID' => $this->option('billID'),
            'amoManagerID' => $this->option('amoManagerID'),
            'leadDBId' => $this->option('leadDBId'),
            'responsible_user_id' => $this->option('responsible_user_id'),
            'direction' => $this->option('direction'),
            'specDoc' => $this->option('specDoc'),
            'fioDoc' => $this->option('fioDoc'),
            'fioPat' => $this->option('fioPat'),
            'filial' => $this->option('filial'),
            'date' => $this->option('date'),
            'billSum' => $this->option('billSum'),
            'offers' => $this->option('offers'),
            'managerName' => $this->option('managerName'),
            'updated_at' => $this->option('updated_at'),
            'created_at' => $this->option('created_at'),
            'responsibleFIO' => $this->option('responsibleFIO'),
            'declareCall' => $this->option('declareCall'),
            'declareVisit' => $this->option('declareVisit'),
        ];
        if ($options) {
            foreach ($options as $optionKey => &$option){
                if ($option === 'null'){
                    $options[$optionKey] = NULL;
                }
            }
            unset($option);
            if ($options){
                dispatch(new CreateLeadJob($options))->onQueue('createLead');
            }
        }
    }
}
