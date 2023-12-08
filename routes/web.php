<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ToAmo\GuzzleToAmo;
use App\Models\PATIENTS;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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
//    dd(PATIENTS::factory()->create());
    return view('welcome');
});

Route::get('/g', function(){
    print_r(DB::select('SELECT SUSER_NAME();'));
//    print_r($response);
});
