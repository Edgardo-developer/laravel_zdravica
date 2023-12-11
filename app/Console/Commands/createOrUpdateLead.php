<?php

namespace App\Console\Commands;

use App\Http\Controllers\SendToAmoCRM;
use App\Models\AmoCrmTable;
use Illuminate\Console\Command;

class createOrUpdateLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:send {dealId}';

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
        $dealID = $this->argument('dealId');
        if ($dealID > 0){
            $SendToAmoCRM->sendDealToAmoCRM($dealID);
        }
    }
}
