<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HtxPriceService;
use App\Services\FeeCalculationService;

class SimulateController extends Controller
{
    public function simulate(
        Request $request,
        HtxPriceService $priceService,
        FeeCalculationService $feeService
    ) {

        $validated = $request->validate([
            'plan' => 'required|in:free,pro',
            'amount' => 'required|numeric|min:100|max:5000000',
            'mode' => 'nullable|in:extended'
        ]);

        try {

            $btcPrice = $priceService->getBtcPriceInr();

            $result = $feeService->calculate(
                $validated['plan'],
                $validated['amount'],
                $btcPrice,
                $request->mode === 'extended'
            );

            return response()->json($result);

        } catch (\Exception $e) {

            return response()->json([
                'message' =>
                    'Unable to fetch live market price.'
            ], 503);
        }
    }
}