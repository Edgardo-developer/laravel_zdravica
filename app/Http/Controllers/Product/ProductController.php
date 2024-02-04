<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LeadLinks\LeadLinksController;
use App\Models\AmoProducts;
use GuzzleHttp\Psr7\Response;

class ProductController extends Controller
{
    private LeadLinksController $LeadLinksController;
    protected ProductRequestController $ProductRequestController;
    private ProductPresendController $ProductPresendController;

    public function __construct($client){
        $this->client = $client;
        $this->ProductPresendController = new ProductPresendController();
        $this->ProductRequestController = new ProductRequestController();
        $this->LeadLinksController = new LeadLinksController($this->client);
    }

    protected function builder(string $offerName): array
    {
        $offerRaw = amoProducts::where('name', '=', $offerName);
        if ($offerRaw->count() > 0) {
            return ['amoID' => $offerRaw->first()->amoID];
        }
        return ['amoID' => 0];
    }

    public function prepare(array $offers) : array{
        $products = [];
        foreach ($offers as $offer) {
            $products[] = [
                'name' => $offer,
                'custom_fields_values' => [
                    [
                        'field_id' => 1550012,
                        'values' => [['value' => 'Все товары']]
                    ]
                ]
            ];
        }
        return $products;
    }

    public function getAmoIDs(array $amoProductNames) : array{
        $checkThem = $this->checkUndefined($amoProductNames);
        $undefinedAmo = $checkThem['undefinedAmo'];
        $ids = $checkThem['ids'];
        if (count($undefinedAmo) > 0) {
            $prepared = $this->prepare($undefinedAmo);
            $newIds = $this->create($prepared);
            $this->ProductPresendController->saveToDB($undefinedAmo, $newIds);
            $ids = array_merge($checkThem['ids'], $newIds);
        }
        return $ids;
    }

    public function create(array $preparedData) : array{
        return $this->ProductRequestController->create($this->client, $preparedData);
    }

    public function update(array $preparedData): void
    {
        $this->ProductRequestController->update($this->client, $preparedData);
    }

    private function checkUndefined(array $amoProductNames): array
    {
        $undefinedAmo = [];
        $ids = [];
        foreach ($amoProductNames as $amoProductName) {
            $amoID = $this->builder($amoProductName);
            if ($amoID['amoID'] === 0) {
                $undefinedAmo[] = $amoProductName;
            } else {
                $ids[] = $amoID['amoID'];
            }
        }
        return [
            'ids'   => $ids,
            'undefinedAmo' => $undefinedAmo
        ];
    }

    /**
     * @param $amoLeadID
     * @param array $offersData
     * @return Response|array
     */
    public function setProducts($amoLeadID, array $offersData): Response|array
    {
        if (count($offersData['offerNames']) > 0){
            $productIDs = $this->getAmoIDs($offersData['offerNames']);
            $linksPrepared = $this->LeadLinksController->prepareAll($productIDs);
            return $this->LeadLinksController->update($linksPrepared,$amoLeadID);
        }
        return [];
    }
}