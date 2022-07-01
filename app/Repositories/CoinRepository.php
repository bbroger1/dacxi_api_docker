<?php

namespace App\Repositories;

use App\Models\Coin;
use App\Repositories\CoinRepositoryInterface;
use App\Repositories\EloquentRepository;

class CoinRepository extends EloquentRepository implements CoinRepositoryInterface
{

    /**
     * Set Coin Model
     * @var $model
     */
    protected $model = Coin::class;


    public function getCoinPrice($price_date, $coin_id, $currency)
    {
        $query = $this->newQuery();
        return $query->select([
            'id', 'coin_id', 'symbol', 'name', 'price', 'price_date', 'currency'
        ])
            ->where('price_date', $price_date)
            ->where('coin_id', $coin_id)
            ->where('currency', $currency)
            ->first();
    }

    /**
     * Service Create dbCoin
     * @param array $dbCoin
     * @return Builder
     */
    public function create($dbCoin)
    {
        $query = $this->newQuery();
        return $query->create($dbCoin);
    }
}
