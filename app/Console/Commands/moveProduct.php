<?php

namespace App\Console\Commands;

use App\Http\Controllers\Product\ProductPrepareController;
use App\Http\Controllers\Product\ProductRequestController;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class moveProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:moveProduct
    {--update=true}
    {--name=null}
    {--amoID=null}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'moves the offer to the AmoCRM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('name') !== 'null') {
            $client = new Client();
            $prepared = ProductPrepareController::prepare([
                'name' => $this->option('name')
            ], 1);

            if ($this->option('update')) {
                $prepared['amoID'] = $this->option('amoID');
                ProductRequestController::update($client, $prepared);
            } else {
                $amoID = ProductRequestController::create($client, $prepared);
                if ($amoID) {
                    echo $amoID[0];
                }
            }
        }
    }
}
