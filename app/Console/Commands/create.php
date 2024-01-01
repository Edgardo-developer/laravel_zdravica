<?php

namespace App\Console\Commands;

use App\Http\Controllers\SendToAmoCRM;
use App\Models\AmoCrmTable;
use Illuminate\Console\Command;

class create extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:sendLead
    {--id=null}
    {--direction=null}
    {--updated_at=null}
    {--created_at=null}
    {--specDoc=null}
    {--patID=null}
    {--fioDoc=null}
    {--declareVisit=null}
    {--declareCall=null}
    {--filial=null}
    {--date=null}
    {--billID=null}
    {--billSum=null}
    {--offers=null}
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
    public function handle(SendToAmoCRM $SendToAmoCRM)
    {
        $options = [
            'direction' => $this->option('direction'),
            'id' => $this->option('id'),
            'specDoc' => $this->option('specDoc'),
            'patID' => $this->option('patID'),
            'fioDoc' => $this->option('fioDoc'),
            'declareVisit' => $this->option('declareVisit'),
            'filial' => $this->option('filial'),
            'date' => $this->option('date'),
            'billID' => $this->option('billID'),
            'billSum' => $this->option('billSum'),
            'offers' => $this->option('offers'),
            'managerName' => $this->option('managerName'),
            'amoManagerID' => $this->option('amoManagerID'),
            'leadDBId' => $this->option('leadDBId'),
            //'amoLeadID' => $this->option('amoLeadID'),
            'updated_at' => $this->option('updated_at'),
            'created_at' => $this->option('created_at'),
            //'amoContactID' => $this->option('amoContactID'),
            'responsibleFIO' => $this->option('responsibleFIO'),
            'responsible_user_id' => $this->option('responsible_user_id'),
            'declareCall' => $this->option('declareCall'),
        ];
        if ($options){
            $SendToAmoCRM->sendDealToAmoCRM($options);
        }
    }
}
