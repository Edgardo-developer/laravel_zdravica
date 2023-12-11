<?php

namespace App\Console\Commands;

use App\Http\Controllers\SendToAmoCRM;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class deleteLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:delete {dealAmoIDs}';

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
        $dealAmoIDsArray = explode(',', $this->argument('dealAmoIDs'));

        if (count($dealAmoIDsArray) > 0){
            $client = new Client(['verify' => false]);
            foreach ($dealAmoIDsArray as $dealAmoID){
                $ID = (int)$dealAmoID;
                if ($ID > 0){
                    $sendDealToAmoCRM->closeLead($client, $ID);
                }
            }
        }
    }
}
