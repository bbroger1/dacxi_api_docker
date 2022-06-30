<?php

namespace App\Http\Controllers;

use App\Services\CoinGeckoApi;
use Illuminate\Http\Request;

class CoinController extends Controller
{

    protected $coinService;

    public function __construct(CoinGeckoApi $coinService)
    {
        $this->coinService = $coinService;
    }

    public function getMostRecentPrice(Request $request)
    {
        return  $this->coinService->getMostRecentPrice($request);
    }

    public function getPriceEstimateByDate(Request $request)
    {
        return $this->coinService->getPriceEstimateByDate($request);
    }
}
