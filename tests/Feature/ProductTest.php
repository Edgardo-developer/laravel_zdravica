<?php

namespace Tests\Feature;

use App\Http\Controllers\LeadLinks\LeadLinksController;
use App\Http\Controllers\Product\ProductController;
use App\Models\AmoProducts;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * A basic feature test example.
     */
//    public function testCheckTheProductAmoID(): void
//    {
//        AmoProducts::create([
//            'name'  => 'Примерка колес',
//            'amoID'  => '451691',
//            'DBId'  => '',
//        ]);
//        AmoProducts::create([
//            'name'  => 'Эпидурография',
//            'amoID'  => '460763',
//            'DBId'  => '',
//        ]);
//
//        $client = new Client(['verify'=>false]);
//        $Product = new ProductController($client);
//
//        $ids = $Product->getAmoIDs(['Примерка колес','Эпидурография']);
//        $this->assertEquals(451691,$ids[0]);
//        $this->assertEquals(460763,$ids[1]);
//    }
//
//    /**
//     * A basic feature test example.
//     */
//    public function testUploadTheProduct(): void
//    {
//        $client = new Client(['verify'=>false]);
//        $Product = new ProductController($client);
//
//        $prepared = $Product->prepare(['Новая услуга']);
//        $newIds = $Product->create($prepared);
//        $this->assertGreaterThan(0, $newIds[0]);
//    }

    /**
     * A basic feature test example.
     */
    public function testLinkTheProductToLead(): void
    {
        $offersData = [
            'offerNames'    => [
                'Новая услуга'
            ],
            'offerPrices'    => [
                5000
            ],
        ];
        $leadDB = ['amoContactID'=>20284111];
        $client = new Client(['verify'=>false]);
        $Product = new ProductController($client);
        $response = $Product->setProducts(13620685,$offersData);
        $this->assertEquals(200,$response->getStatusCode());
    }
}
