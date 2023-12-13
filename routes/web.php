<?php

use Amp\ReactAdapter\ReactAdapter;
use App\Http\Controllers\CronAmo;
use App\Models\AmoCrmTable;
use Illuminate\Support\Facades\Route;
use React\EventLoop\Loop;
use React\Promise\Promise;
use Revolt\EventLoop;
use Spatie\Async\Pool;
use VXM\Async\AsyncFacade as Async;

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
//    AmoCrmLead::factory()->create();
//    PATIENTS::factory()->create();

    $CronAmo = new CronAmo();
    $CronAmo->reactOnCron();
    return view('welcome');
});
Route::get('/g', function(){
//    print_r($response);
});
