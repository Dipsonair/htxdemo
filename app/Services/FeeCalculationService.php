<?php

namespace App\Services;

class FeeCalculationService
{
    const HTX_FEE_PERCENT = 0.001; // 0.1%
    const REBATE_PERCENT = 0.60;
    const FREE_VISIBLE_FEE = 0.0004; // 0.04%

    public function calculate(
        string $plan,
        float $amount,
        float $btcPrice,
        bool $extended = false
    ): array {

        $htxFee = $amount * self::HTX_FEE_PERCENT;

        $rebate = $htxFee * self::REBATE_PERCENT;

        $netCost = $htxFee - $rebate;

        $customerPaysFee = $plan === 'free'
            ? $amount * self::FREE_VISIBLE_FEE
            : 0;

        $effectiveAmount = $amount;

        $processingFee = 0;
        $displayedPrice = $btcPrice;
        $capturedSpreadRevenue = 0;

        if ($extended) {

            $processingFee = 8;

            $effectiveAmount -= $processingFee;

            $displayedPrice = $btcPrice * 1.0001;

            $cryptoAtDisplayed = $amount / $displayedPrice;

            $cryptoAtMarket = $effectiveAmount / $btcPrice;

            $capturedSpreadRevenue =
                ($cryptoAtMarket - $cryptoAtDisplayed) * $btcPrice;
        }

        $cryptoReceived = $effectiveAmount / $btcPrice;
        $expectedCrypto = $amount / $displayedPrice;

        $executableAmount = $effectiveAmount;

        return [
            'market_price_inr' => round($btcPrice, 2),

            'displayed_price_inr' => round($displayedPrice, 2),

            'amount_inr' => $amount,

            'htx_fee_inr' => round($htxFee, 2),

            'rebate_inr' => round($rebate, 2),

            'net_cost_to_platform' => round($netCost, 2),

            'customer_pays_fee' => round($customerPaysFee, 2),

            'processing_fee_inr' => round($processingFee, 2),

            'captured_spread_revenue_inr' =>
                round($capturedSpreadRevenue, 2),

            'final_crypto_received' =>
                round($cryptoReceived, 8),

            'extended_mode' => $extended, 
            'expected_crypto_received' =>
                round($expectedCrypto, 8),

            'executable_amount' =>
                round($executableAmount, 2),
        ];
    }
}