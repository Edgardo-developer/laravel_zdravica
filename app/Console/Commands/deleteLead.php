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
    public function handle(string $dealAmoIDs)
    {
        $dealAmoIDsArray = explode(',', $dealAmoIDs);

        if (count($dealAmoIDsArray > 0)){
            $sendDealToAmoCRM = new SendToAmoCRM();
            $client = new Client();
            foreach ($dealAmoIDsArray as $dealAmoID){
                $sendDealToAmoCRM->closeLead($client, $dealAmoID);
            }
        }
    }
}
