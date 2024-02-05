<?php

namespace App\Console\Commands\Leads;

use App\Jobs\UpdateLeadJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class updateLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:updateLead
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
    {--offerLists=null}
    {--managerName=null}
    {--amoManagerID=null}
    {--leadDBId=null}
    {--amoLeadID=null}
    {--amoContactID=null}
    {--responsibleFIO=null}
    {--responsible_user_id=null}
    {--patID_changed=false}
';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates Lead';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $options = [
            'id' => $this->option('id'),
            'patID' => $this->option('patID'),
            'patID_changed' => $this->option('patID_changed'),
            'billID' => $this->option('billID'),
            'amoManagerID' => $this->option('amoManagerID'),
            'leadDBId' => $this->option('leadDBId'),
            'responsible_user_id' => $this->option('responsible_user_id'),
            'direction' => $this->option('direction'),
            'specDoc' => $this->option('specDoc'),
            'fioDoc' => $this->option('fioDoc'),
            'filial' => $this->option('filial'),
            'date' => $this->option('date'),
            'billSum' => $this->option('billSum'),
            'offers' => $this->option('offers'),
            'offerLists' => $this->option('offerLists'),
            'managerName' => $this->option('managerName'),
            'updated_at' => $this->option('updated_at'),
            'created_at' => $this->option('created_at'),
            'responsibleFIO' => $this->option('responsibleFIO'),
            'declareCall' => $this->option('declareCall'),
            'declareVisit' => $this->option('declareVisit'),
        ];

        if ($options){
            Log::info('leadDBId: '.$options['leadDBId'] . ' UPDATE');
            foreach ($options as $optionKey => &$option){
                if ($option === 'null'){
                    $options[$optionKey] = NULL;
                }
            }
            unset($option);
            if ($options){
                dispatch(new UpdateLeadJob($options));
            }
        }
    }
}
