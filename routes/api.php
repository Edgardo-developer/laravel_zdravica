<?php

use App\Http\Controllers\AmoSide;
use App\Http\Controllers\SubdSide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/createSubdPatient', [SubdSide::class, 'createPatient']);
Route::post('/createSubdLead', [SubdSide::class, 'createLead']);

Route::post('/createAmoPatient', [AmoSide::class, 'createPatient']);
Route::post('/createAmoLead', [AmoSide::class, 'createLead']);
