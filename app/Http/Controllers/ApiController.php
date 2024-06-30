<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    private $apiUrl = 'https://blockchain.info/ticker';
    private $commission = 0.02; // 2% комиссия

    public function handleRequest(Request $request)
    {
        $method = $request->query('method');

        switch ($method) {
            case 'rates':
                return $this->getRates($request);
            case 'convert':
                return $this->convert($request);
            default:
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Invalid method'
                ], 400);
        }
    }

    private function getRates(Request $request)
    {
        $response = Http::get($this->apiUrl);
        $rates = $response->json();

        $processedRates = [];
        foreach ($rates as $currency => $data) {
            $processedRates[$currency] = $data['last'] * (1 + $this->commission);
        }

        asort($processedRates);

        $currencies = $request->query('currency');
        if ($currencies) {
            $currencies = explode(',', $currencies);
            $processedRates = array_intersect_key($processedRates, array_flip($currencies));
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $processedRates
        ]);
    }

    private function convert(Request $request)
    {
        $currencyFrom = $request->input('currency_from');
        $currencyTo = $request->input('currency_to');
        $value = floatval($request->input('value'));

        if ($value < 0.01) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Minimum exchange amount is 0.01'
            ], 400);
        }

        $response = Http::get($this->apiUrl);
        $rates = $response->json();

        if ($currencyFrom === 'BTC') {
            $rate = $rates[$currencyTo]['last'] * (1 + $this->commission);
            $convertedValue = $value * $rate;
            $convertedValue = round($convertedValue, 2);
        } else {
            $rate = (1 / $rates[$currencyFrom]['last']) * (1 + $this->commission);
            $convertedValue = $value * $rate;
            $convertedValue = round($convertedValue, 10);
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => [
                'currency_from' => $currencyFrom,
                'currency_to' => $currencyTo,
                'value' => $value,
                'converted_value' => $convertedValue,
                'rate' => $rate
            ]
        ]);
    }
}
