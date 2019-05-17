<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::get( 'comercial/consultores', 'ComercialController@ConsultoresList');
Route::get( 'comercial/annos', 'ComercialController@AnnosList');
Route::post( 'comercial/relatorio', 'ComercialController@Relatorio');
Route::post( 'comercial/grafico', 'ComercialController@Grafico');
Route::post( 'comercial/pizza', 'ComercialController@Pizza');