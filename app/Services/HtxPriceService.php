<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HtxPriceService
{
    const USDT_TO_INR = 86;

    public function getBtcPriceInr(): float
    {
        $response = Http::timeout(10)
            ->get('https://api.huobi.pro/market/detail/merged', [
                'symbol' => 'btcusdt'
            ]);

        if (!$response->successful()) {
            throw new \Exception('Unable to fetch HTX price');
        }

        $data = $response->json();

        $btcUsdt = $data['tick']['close'];

        return $btcUsdt * self::USDT_TO_INR;
    }
}