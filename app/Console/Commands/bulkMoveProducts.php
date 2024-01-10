<?php

namespace App\Console\Commands;

use App\Http\Controllers\Product\ProductPrepareController;
use App\Http\Controllers\Product\ProductRequestController;
use App\Models\OffersDB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class bulkMoveProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:bulk-move-products';

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
        $offers = OffersDB::all()->toArray();
        $products = ProductPrepareController::prepare($offers, 1);

        $client = new Client(['verify'=>false]);
        $proids = ProductRequestController::create($client, $products);
    }
}
