<?php

use App\Http\Controllers\BeneficioController;
use App\Http\Controllers\FichaController;
use App\Http\Resources\FichaCollection;
use App\Models\Ficha;
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
    return view('welcome');
});

Route::group(['prefix' => 'api', 'namespace' => 'App\Http\Controllers'], function(){
    Route::apiResource('beneficios',BeneficioController::class);
});