<?php

namespace App\Console\Commands;

use App\Models\OffersDB;
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
    }
}
