<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ExternalApiController extends Controller
{
    public function index()
    {
        $apiKey = 'demo';
        $symbol = 'IBM';


        $interval = '5min';
        $intradayUrl = "https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&symbol={$symbol}&interval={$interval}&apikey={$apiKey}";
        $intradayResponse = Http::withoutVerifying()->get($intradayUrl);
        $intradayData = $intradayResponse->json();
        $meta = $intradayData['Meta Data'] ?? [];
        $series = $intradayData['Time Series (5min)'] ?? [];
        $chartData = [];
        foreach ($series as $time => $values) {
            $chartData[] = [
                'time' => $time,
                'open' => (float)$values['1. open'],
                'high' => (float)$values['2. high'],
                'low' => (float)$values['3. low'],
                'close' => (float)$values['4. close'],
                'volume' => (int)$values['5. volume'],
            ];
        }
        $chartData = array_reverse($chartData);

        $dailyUrl = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol={$symbol}&apikey={$apiKey}";
        $dailyResponse = Http::withoutVerifying()->get($dailyUrl);
        $dailyData = $dailyResponse->json();
        $dailyMeta = $dailyData['Meta Data'] ?? [];
        $dailySeries = $dailyData['Time Series (Daily)'] ?? [];
        $dailyChartData = [];
        foreach ($dailySeries as $date => $values) {
            $dailyChartData[] = [
                'date' => $date,
                'open' => (float)$values['1. open'],
                'high' => (float)$values['2. high'],
                'low' => (float)$values['3. low'],
                'close' => (float)$values['4. close'],
                'volume' => (int)$values['5. volume'],
            ];
        }
        $dailyChartData = array_reverse($dailyChartData);

      
        $adjustedUrl = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY_ADJUSTED&symbol={$symbol}&apikey={$apiKey}";
        $adjustedResponse = Http::withoutVerifying()->get($adjustedUrl);
        $adjustedData = $adjustedResponse->json();
        $adjustedMeta = $adjustedData['Meta Data'] ?? [];
        $adjustedSeries = $adjustedData['Time Series (Daily)'] ?? [];
        $adjustedChartData = [];
        foreach ($adjustedSeries as $date => $values) {
            $adjustedChartData[] = [
                'date' => $date,
                'open' => (float)$values['1. open'],
                'high' => (float)$values['2. high'],
                'low' => (float)$values['3. low'],
                'close' => (float)$values['4. close'],
                'adjusted_close' => (float)$values['5. adjusted close'],
                'volume' => (int)$values['6. volume'],
                'dividend' => (float)$values['7. dividend amount'],
                'split' => (float)$values['8. split coefficient'],
            ];
        }
        $adjustedChartData = array_reverse($adjustedChartData);

        return view('external', [
            'meta' => $meta,
            'chartData' => $chartData,
            'dailyMeta' => $dailyMeta,
            'dailyChartData' => $dailyChartData,
            'adjustedMeta' => $adjustedMeta,
            'adjustedChartData' => $adjustedChartData,
        ]);
    }
}
