<?php

namespace App\Console\Commands;


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
    {--id=null}
    {--ULICA=null}
    {--RAYON_VYBORKA=null}
    {--NUMBER=null}
    {--DOM=null}
    {--KVARTIRA=null}
    {--DOVERENNI=null}
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
            'id' => $this->option('id') ?? '',
            'ULICA' => $this->option('ULICA') ?? '',
            'RAYON_VYBORKA' => $this->option('RAYON_VYBORKA') ?? '',
            'NUMBER' => $this->option('NUMBER') ?? '',
            'DOM' => $this->option('DOM') ?? '',
            'KVARTIRA' => $this->option('KVARTIRA') ?? '',
            'Doverenni' => $this->option('DOVERENNI') ?? '',
        ];
        if ($options['id']) {
            $amoID = $options['id'];
            $arr = ['Д', 'Ш', 'КМ', 'ДК', 'ГР', 'Т', 'О'];
            if ($options['NUMBER'] && (int)$options['NUMBER'] < 7) {
                $options['NUMBER'] = $arr[(int)$options['NUMBER']];
            }
            $prepared = ContactsPrepareController::prepare($options, 1);
            if ($prepared) {
                $prepared['amoID'] = $amoID;
                $client = new Client(['verify' => false]);
                ContactsRequestController::update($client, $prepared);
            }
        }
    }
}
