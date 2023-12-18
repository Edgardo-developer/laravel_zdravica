<?php


use App\Http\Controllers\CronAmo;
use App\Models\AmoCrmLead;
use Illuminate\Support\Facades\Route;

use function Amp\delay;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    AmoCrmLead::factory()->create();
//    PATIENTS::factory()->create();
//    phpinfo();
    // While we create leads, we should put the logic:
    // - Create all contacts within one request and save them to each DB lead
    // - Create all leads within one request including the contact ID
//    $CronAmo = new CronAmo();
//    $CronAmo->reactOnCron();

    echo PHP_EOL;
    return view('welcome');
});
Route::get('/g', function(){
//    print_r($response);
});
