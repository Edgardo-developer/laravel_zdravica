<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\SendToAmoCRM;
use App\Http\Controllers\ToAmo\GuzzleToAmo;
use App\Models\AmoCrmLead;
use App\Models\PATIENTS;
use App\Models\Post;
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
    $SendToAmoCRM = new SendToAmoCRM();
    $SendToAmoCRM->sendDealToAmoCRM(1);
    return view('welcome');
});

Route::get('/g', function(){
//    print_r($response);
});
