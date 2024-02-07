<?php

namespace App\Jobs;

use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductPrepareController;
use App\Http\Controllers\Product\ProductRequestController;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $name;
    protected $update;
    protected $amoID;
    /**
     * Create a new job instance.
     */
    public function __construct($name, $update, $amoID)
    {
        $this->name = $name;
        $this->update = $update;
        $this->amoID = $amoID;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client = new Client();
        $ProductController = new ProductController($client);
        $prepared = $ProductController->prepare([
            'name' => $this->name
        ]);

        if ($this->update) {
            $prepared['amoID'] = $this->amoID;
            ProductRequestController::update($client, $prepared);
        } else {
            $amoID = ProductRequestController::create($client, $prepared);
            if ($amoID) {
                echo $amoID[0];
            }
        }
    }
}
