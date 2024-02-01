<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\AmoProducts;

class ProductGeneralController extends Controller
{
    public function __construct($client){
        $this->client = $client;
        $this->ProductPresendController = new ProductPresendController($client);
        $this->ProductRequestController = new ProductRequestController();
    }

    protected function builder(string $offerName): array
    {
        $offerRaw = amoProducts::where('name', '=', $offerName);
        if ($offerRaw->count() > 0) {
            return ['amoID' => $offerRaw->first()->amoID];
        }
        return ['amoID' => 0];
    }

    protected function prepare($offers) : array{
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

    public function getAmoIDs($amoProductNames) : array{
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

    public function create($preparedData) : array{
        return $this->ProductRequestController->create($this->client, $preparedData);
    }

    public function update($preparedData): void
    {
        $this->ProductRequestController->update($this->client, $preparedData);
    }

    private function checkUndefined($amoProductNames): array
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
}
