<?php

namespace App\Repositories;

interface CoinRepositoryInterface
{
    public function getCoinPrice($price_date, $coin_id);

    public function create($dbCoin);
}
