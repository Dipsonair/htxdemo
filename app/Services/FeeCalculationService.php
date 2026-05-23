<?php

namespace App\Services;

class FeeCalculationService
{
    const HTX_FEE_PERCENT = 0.001; // 0.1%
    const REBATE_PERCENT = 0.60;
    const FREE_VISIBLE_FEE = 0.0004; // 0.04%
    const PROCESSING_FEE = 8; // ₹8 flat
    const PRICE_ADJUSTMENT_PERCENT = 0.0001; // 0.01%

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
        $priceAdjustmentRevenue = 0;

        if ($extended) {
            // 1. Execution Processing Fee: deduct ₹8 internally
            $processingFee = self::PROCESSING_FEE;
            $effectiveAmount = $amount - $processingFee;

            // 2. Displayed Price Adjustment: inflate by 0.01%
            $displayedPrice = $btcPrice * (1 + self::PRICE_ADJUSTMENT_PERCENT);

            // Revenue from price spread on the effective amount
            // Customer thinks they buy at displayedPrice, trade executes at btcPrice
            $cryptoAtDisplayed = $effectiveAmount / $displayedPrice;
            $cryptoAtMarket = $effectiveAmount / $btcPrice;
            $priceAdjustmentRevenue = ($cryptoAtMarket - $cryptoAtDisplayed) * $btcPrice;
        }

        // What the customer actually receives (based on displayed price from their perspective)
        // In extended mode: customer sees amount / displayedPrice
        // In core mode: customer gets effectiveAmount / btcPrice (same as amount / btcPrice)
        $customerCryptoReceived = $extended
            ? $amount / $displayedPrice
            : $effectiveAmount / $btcPrice;

        // Actual crypto executed internally at true market price
        $actualCryptoExecuted = $effectiveAmount / $btcPrice;

        // Total platform revenue in extended mode
        $totalPlatformRevenue = $processingFee + $priceAdjustmentRevenue;

        return [
            'market_price_inr' => round($btcPrice, 2),
            'displayed_price_inr' => round($displayedPrice, 2),
            'amount_inr' => $amount,
            'htx_fee_inr' => round($htxFee, 2),
            'rebate_inr' => round($rebate, 2),
            'net_cost_to_platform' => round($netCost, 2),
            'customer_pays_fee' => round($customerPaysFee, 2),
            'processing_fee_inr' => round($processingFee, 2),
            'price_adjustment_revenue_inr' => round($priceAdjustmentRevenue, 2),
            'total_platform_revenue_inr' => round($totalPlatformRevenue, 2),
            'final_crypto_received' => round($customerCryptoReceived, 8),
            'actual_crypto_executed' => round($actualCryptoExecuted, 8),
            'expected_crypto_received' => round($amount / $displayedPrice, 8),
            'executable_amount' => round($effectiveAmount, 2),
            'extended_mode' => $extended,
        ];
    }
}