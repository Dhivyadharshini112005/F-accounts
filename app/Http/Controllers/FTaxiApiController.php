<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class FTaxiApiController extends Controller
{
    public function test()
    {
        $url = env('FTAXI_BASE_URL') . '/company/driver_payout/all';

        $params = [
            'draw' => 1,
            'start' => 0,
            'length' => 10,

            'columns[0][data]' => 'id_number',
            'columns[0][name]' => 'users.id_number',
            'columns[0][searchable]' => 'true',
            'columns[0][orderable]' => 'true',
            'columns[0][search][value]' => '',
            'columns[0][search][regex]' => 'false',

            'columns[1][data]' => 'frequency',
            'columns[1][name]' => 'generate_driver_payments.frequency',
            'columns[1][searchable]' => 'false',
            'columns[1][orderable]' => 'true',
            'columns[1][search][value]' => '',
            'columns[1][search][regex]' => 'false',

            'columns[2][data]' => 'month',
            'columns[2][name]' => 'generate_driver_payments.month',
            'columns[2][searchable]' => 'false',
            'columns[2][orderable]' => 'true',
            'columns[2][search][value]' => '',
            'columns[2][search][regex]' => 'false',

            'order[0][column]' => 0,
            'order[0][dir]' => 'desc',

            'search[value]' => '',
            'search[regex]' => 'false',
        ];

        $response = Http::timeout(60)
            ->withOptions([
                'verify' => false,
            ])
            ->withCookies([
                'PHPSESSID' => env('FTAXI_PHPSESSID'),
                'XSRF-TOKEN' => env('FTAXI_XSRF_TOKEN'),
                'friendstrack_ellancab_session' => env('FTAXI_SESSION'),
            ], 'book.ftaxi.in')
            ->withHeaders([
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'X-Requested-With' => 'XMLHttpRequest',
                'Referer' => 'https://book.ftaxi.in/company/driver_payout/all',
            ])
            ->get($url, $params);

        return response()->json([
            'status' => $response->status(),
            'content_type' => $response->header('Content-Type'),
            'successful' => $response->successful(),
            'data' => $response->json(),
        ]);
    }
}