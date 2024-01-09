<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class moveProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:move-product
    {--update=false}
    {--name=null}
    {--amocrmID=null}
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
        //
    }
}
