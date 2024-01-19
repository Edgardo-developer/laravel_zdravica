<?php

namespace App\Console\Commands;


use App\Http\Controllers\Contacts\ContactsBuilderController;
use App\Http\Controllers\Contacts\ContactsPrepareController;
use App\Http\Controllers\Contacts\ContactsRequestController;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class updateContact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:updateContact
    {--PAT_ID=null}
    {--declareCall=null}
    ';

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
        $options = [
            'PAT_ID' => $this->option('id') ?? '',
            'declareCall' => $this->option('declareCall') ?? '',
        ];
        if ($options['PAT_ID'] !== '' && (int)$options['declareCall'] > 0) {
            $row = ContactsBuilderController::getRow(
                (int)$options['PAT_ID'],
                (int)$options['declareCall'] === 1
            );
            $prepared = ContactsPrepareController::prepare($row);
            $client = new Client(['verify' => false]);
            if ($prepared){
                ContactsRequestController::update($client, $prepared);
            }
        }
        }
    }
