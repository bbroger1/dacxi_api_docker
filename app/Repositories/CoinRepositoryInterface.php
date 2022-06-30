<?php

namespace App\Repositories;

interface CoinRepositoryInterface
{
    public function getCoinPrice($price_date);

    public function create($dbCoin);
}
