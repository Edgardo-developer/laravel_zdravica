<?php

namespace App\Console\Commands;

use App\Http\Controllers\Product\ProductPrepareController;
use App\Http\Controllers\Product\ProductRequestController;
use App\Jobs\CreateLeadJob;
use App\Jobs\ProcessProduct;
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
            dispatch(new ProcessProduct(
                $this->option('name'),
                $this->option('update'),
                $this->option('amoID')))->onQueue('moveProduct');
        }
    }
}
