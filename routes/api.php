<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoinController;

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

Route::post('/coin', [CoinController::class, 'getMostRecentPrice']);
Route::post('/coin/price-estimated-date', [CoinController::class, 'getPriceEstimateByDate']);
