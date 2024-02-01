<?php

namespace App\Console\Commands;

use App\Http\Controllers\Product\ProductGeneralController;
use App\Http\Controllers\Product\ProductPrepareController;
use App\Http\Controllers\Product\ProductRequestController;
use App\Models\AmoProducts;
use App\Models\OffersDB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class BulkProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:bulkProducts';

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
        $offers = OffersDB::all(['name', 'id'])->toArray();
        $offersChunks = array_chunk($offers, 40);
        $client = new Client(['verify' => false]);

        $ProductGeneralController = new ProductGeneralController($client);
        foreach ($offersChunks as $offersChunk) {
            $products = $ProductGeneralController->prepare($offersChunk, 1);
            $proids = $ProductGeneralController->create($client, $products);
            $amoProduct = [];
            foreach ($offersChunk as $k => $product) {
                $amoProduct[] = [
                    'name' => $product['name'],
                    'DBId' => $product['id'],
                    'amoID' => $proids[$k]['id'],
                ];
            }
            AmoProducts::create($amoProduct);
        }
    }
}
