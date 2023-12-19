<?php

namespace App\Console\Commands;

use App\Http\Controllers\Contacts\ContactsBuilderController;
use App\Http\Controllers\Contacts\ContactsPrepareController;
use App\Http\Controllers\Contacts\ContactsRequestController;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class updateContact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradeal:update_contact
    {--id=null}
    {--ULICA=null}
    {--RAYON_VYBORKA=null}
    {--NUMBER=null}
    {--DOM=null}
    {--KVARTIRA=null}
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
            'id' => $this->option('amoID'),
            'ULICA' => $this->option('ULICA'),
            'RAYON_VYBORKA' => $this->option('RAYON_VYBORKA'),
            'NUMBER' => $this->option('NUMBER'),
            'DOM' => $this->option('DOM'),
            'KVARTIRA' => $this->option('KVARTIRA'),
        ];
        if ($options['amoID']){
            $amoID = DB::table('PLANNING')->where('amoContactID', '=', $options['amoID'])->first();
            $prepared = ContactsPrepareController::prepare($options);

            if ($amoID && $prepared){
                $prepared['id'] = $amoID;
                $client = new Client();
                ContactsRequestController::update($client, $prepared);
            }
        }
    }
}
