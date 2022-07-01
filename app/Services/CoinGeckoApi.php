<?php

namespace App\Services;

use App\Repositories\CoinRepository;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Validator;

class CoinGeckoApi
{
    private $clientCoinGeckoApi;
    private $coinsAccepted;

    public function __construct(CoinRepository $coinRepository)
    {
        $this->coinRepository = $coinRepository;
        $this->clientCoinGeckoApi =  new CoinGeckoClient();
        $this->coinsAccepted = [
            'bitcoin', 'ethereum', 'dacxi', 'cosmos', 'terra-luna'
        ];
        $this->currencyAccepted = [
            'brl', 'eur', 'usd'
        ];
    }

    public function getMostRecentPrice($request)
    {
        $validator = Validator::make($request->all(), [
            'coin_id' => 'required',
            'currency' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'message' => 'Please follow instructions',
                    'fields' => $validator->errors()->getMessages()
                ]
            ]);
        }

        $request->coin_id = strtolower($request->coin_id);
        $request->currency = strtolower($request->currency);

        if (!in_array($request->coin_id, $this->coinsAccepted)) {
            return response()->json([
                'message' => 'Please select an accepted coin: Bitcoin, Ethereum, Dacxi, Cosmos or Terra-luna'
            ], 404);
        }

        if (!in_array($request->currency, $this->currencyAccepted)) {
            return response()->json([
                'message' => 'Please select an accepted currency: BRL, EUR or USD'
            ], 404);
        }

        try {
            $coinPrice = $this->clientCoinGeckoApi->simple()
                ->getPrice($request->coin_id, $request->currency);

            $coinData = $this->clientCoinGeckoApi->coins()->getCoin($request->coin_id, [
                'tickers'     => false,
                'market_data' => false,
            ]);

            $dbCoin = [
                'coin_id'    => $request->coin_id,
                'symbol'     => $coinData['symbol'],
                'name'       => $coinData['name'],
                'price'      => $coinPrice[$request->coin_id][$request->currency],
                'price_date' => date('Y-m-d H:i'),
                'currency'   => $request->currency,
            ];

            $this->coinRepository->create($dbCoin);
        } catch (ClientException $clientErr) {
            return response()->json(($clientErr->getMessage()), $clientErr->getCode());
        } catch (\PDOException $err) {
            return response()->json($err);
        }

        return response()->json($coinPrice);
    }

    public function getPriceEstimateByDate($request)
    {
        $validator = Validator::make($request->all(), [
            'coin_id'  => 'required',
            'datetime' => 'required',
            'currency' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'message' => 'Please follow instructions',
                    'fields' => $validator->errors()->getMessages()
                ]
            ]);
        }

        $request->coin_id = strtolower($request->coin_id);
        $request->currency = strtolower($request->currency);

        if (!in_array($request->coin_id, $this->coinsAccepted)) {
            return response()->json([
                'message' => 'Please select an accepted coin: Bitcoin, Ethereum, Dacxi, Cosmos or Terra-luna'
            ], 404);
            die;
        }

        if (!in_array($request->currency, $this->currencyAccepted)) {
            return response()->json([
                'message' => 'Please select an accepted currency: BRL, EUR or USD'
            ], 404);
            die;
        }

        try {
            $date_find = strtotime($request->datetime);
            $date_current = strtotime(date('Y-m-d H:i'));

            if ($date_find > $date_current) {
                return response()->json([
                    'error' => [
                        'message' => 'The datetime must be earlier than the current date and time'
                    ]
                ], 400);
            }

            if (!$data = $this->coinRepository->getCoinPrice($request->datetime, $request->coin_id, $request->currency)) {
                $result = $this->clientCoinGeckoApi->coins()->getMarketChartRange(
                    $request->coin_id,
                    $request->currency,
                    $date_find,
                    $date_current
                );

                $coin_data = $this->find_data(
                    $result['prices'],
                    $request->datetime,
                    $request->coin_id,
                    $request->currency
                );

                $coin = $this->clientCoinGeckoApi->coins()->getCoin($request->coin_id, [
                    'tickers'     => false,
                    'market_data' => false,
                ]);

                $dbCoin = [
                    'coin_id'    => $coin['id'],
                    'symbol'     => $coin['symbol'],
                    'name'       => $coin['name'],
                    'price'      => $coin_data[$coin['id']][$request->currency],
                    'price_date' => $request->datetime,
                    'currency'   => $request->currency,
                ];

                $this->coinRepository->create($dbCoin);

                return response()->json($coin_data);
            }

            $coin_data = [
                $data['coin_id'] => [
                    $data['currency'] => $data['price'],
                    'datetime' => date('Y-m-d H:i', $data['price_id']),
                ]
            ];
        } catch (ClientException $clientErr) {
            if (strpos($clientErr->getMessage(), 'invalid date') !== false) {
                return response()->json([
                    'message' => 'Invalid date format. Expected format: dd-mm-yyyy',
                ], $clientErr->getCode());
            } else if (strpos($clientErr->getMessage(), 'not find coin') !== false) {
                return response()->json([
                    'message' => 'Please select an accepted coin: Bitcoin, Ethereum, Dacxi, Cosmos or Terra-luna',
                ], $clientErr->getCode());
            } else {
                return response()->json($clientErr->getMessage());
            }
        }

        return response()->json($coin_data);
    }

    private function find_data($array, $findDate, $coin_id, $currency)
    {
        $msDates = array();

        foreach ($array as $index => $date) {
            $msDates[] = $date[0];

            foreach ($msDates as $a) {
                if ($a >= strtotime($findDate)) {
                    $estimated = [
                        $coin_id => [
                            'datetime' => date('Y-m-d H:i', $array[$index][0] / 1000),
                            $currency => number_format($array[$index][1], 2, '.', '')
                        ]
                    ];
                    return $estimated;
                }
            }
        }
    }
}
