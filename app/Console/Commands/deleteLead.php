<?php

namespace App\Console\Commands;

use App\Http\Controllers\Sends\DeleteLeadController;
use Illuminate\Console\Command;

class deleteLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:deleteLead
    {--id=null}
    {--ids=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $options = [
            'id' => $this->option('id'),
            'ids'   => $this->option('ids')
        ];
        if ($options){
            if ($options['id']){
                $ids = [$options['id']];
            }else if ($options['ids']){
                $ids = explode(',',$options['ids']);
            }
            if (isset($ids)){
                $DeleteLeadController = new DeleteLeadController($ids);
                $DeleteLeadController->deleteLeads();
            }
        }
    }
}
