<?php

namespace App\Console\Commands;

use App\Http\Controllers\Product\ProductController;
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
        dd($offers);
        $offersChunks = array_chunk($offers, 40);
        $client = new Client(['verify' => false]);

        $ProductController = new ProductController($client);
        foreach ($offersChunks as $offersChunk) {
            $preparedProducts = $ProductController->prepare($offersChunk, 1);
            $proids = $ProductController->create($preparedProducts);
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
