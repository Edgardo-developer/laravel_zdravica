<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LeadLinks\LeadLinksController;
use App\Models\AmoProducts;
use App\Models\OffersDB;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;

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

    protected function getAmoIDFromDB(string $offerName): int
    {
        $offerDBRaw = (array) OffersDB::where('LABEL',trim($offerName))->first();
        if ($offerDBRaw){
            $product = AmoProducts::where('DBId',$offerDBRaw->FM_SERV_ID)->first();
            if ($product){
                if (trim($offerName) !== trim($product->name)){
                    $product->name = trim($offerName);
                    $product->save();
                }
               return $product->amoID;
            }
        }
        return 0;
    }

    public function prepare(array $offers) : array{
        $products = [];
        foreach ($offers as $offer) {
            if (isset($offer['name']) || isset($offer['LABEL'])){
                $name = $offer['LABEL'] ?? $offer['name'];
                $products[] = [
                    'name' => trim($name),
                    'custom_fields_values' => [
                        [
                            'field_id' => 1550012,
                            'values' => [['value' => 'Все товары']]
                        ],
                        [
                            'field_id'  => 1550006,
                            'values' => [['value' => $offer['CODE']]]
                        ]
                    ]
                ];
            }
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

    public function update(array $preparedData, int $amoProductID): void
    {
        $this->ProductRequestController->update($this->client, $preparedData, $amoProductID);
    }

    private function checkUndefined(array $amoProductNames): array
    {
        $undefinedAmo = [];
        $ids = [];
        foreach ($amoProductNames as $amoProductName) {
            if ($amoProductName !== ''){
                $amoID = $this->getAmoIDFromDB($amoProductName);
                if ($amoID === 0) {
                    $undefinedAmo[] = $amoProductName;
                    continue;
                }
                $ids[] = $amoID;
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
        $productIDs = $this->getAmoIDs($offersData['offerNames']);
        $linksPrepared = $this->LeadLinksController->prepareAll($productIDs);
        return $this->LeadLinksController->update($linksPrepared,$amoLeadID);
    }

    /**
     * @param $amoLeadID
     * @param array $offersData
     * @return Response|array
     */
    public function unsetProducts($amoLeadID, array $offersData): Response|array
    {
        $productIDs = $this->getAmoIDs($offersData['offerNames']);
        $linksPrepared = $this->LeadLinksController->prepareAll($productIDs);
        return $this->LeadLinksController->remove($linksPrepared,$amoLeadID);
    }
}
