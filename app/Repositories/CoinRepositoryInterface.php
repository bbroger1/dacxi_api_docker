<?php

namespace App\Repositories;

interface CoinRepositoryInterface
{
    public function getCoinPrice($price_date, $coin_id, $currency);

    public function create($dbCoin);
}
