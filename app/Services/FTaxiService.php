<?php

namespace App\Services;

use App\Models\FTaxiMonthlyAccount;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;

class FTaxiService
{
    protected $baseUrl;

    protected $cookieJar;

    protected $authenticated = false;

    protected $httpClient;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        $this->baseUrl = rtrim(
            env(
                'FTAXI_BASE_URL',
                'https://book.ftaxi.in'
            ),
            '/'
        );

        $this->cookieJar = new CookieJar();

        /*
         * Use Guzzle directly.
         *
         * IMPORTANT:
         * proxy must be an empty string, not false.
         */
        $this->httpClient = new Client([
            'connect_timeout' => 15,
            'timeout' => 60,
            'verify' => false,
            'allow_redirects' => true,
            'cookies' => $this->cookieJar,
            'proxy' => '',
            'headers' => [
                'User-Agent' =>
                    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) ' .
                    'AppleWebKit/537.36 (KHTML, like Gecko) ' .
                    'Chrome/151.0.0.0 Safari/537.36',

                'Accept' =>
                    'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',

                'Accept-Language' =>
                    'en-US,en;q=0.9',

                'Connection' =>
                    'keep-alive',
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE HTTP CLIENT WITH CURRENT COOKIE JAR
    |--------------------------------------------------------------------------
    */

    protected function createClient()
    {
        return new Client([
            'connect_timeout' => 15,
            'timeout' => 60,
            'verify' => false,
            'allow_redirects' => true,
            'cookies' => $this->cookieJar,
            'proxy' => '',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | F-TAXI LOGIN
    |--------------------------------------------------------------------------
    */

    protected function authenticate()
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Reset authentication state
            |--------------------------------------------------------------------------
            */

            $this->authenticated = false;


            /*
            |--------------------------------------------------------------------------
            | Open Login Page
            |--------------------------------------------------------------------------
            */

            $client = $this->createClient();

            $loginPage = $client->get(
                $this->baseUrl . '/admin/login',
                [
                    'headers' => [
                        'User-Agent' =>
                            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) ' .
                            'AppleWebKit/537.36 (KHTML, like Gecko) ' .
                            'Chrome/151.0.0.0 Safari/537.36',

                        'Accept' =>
                            'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',

                        'Accept-Language' =>
                            'en-US,en;q=0.9',

                        'Connection' =>
                            'keep-alive',
                    ],
                ]
            );


            $loginStatus =
                $loginPage->getStatusCode();

            $loginBody =
                (string) $loginPage->getBody();


            if (
                $loginStatus < 200 ||
                $loginStatus >= 400
            ) {

                return [
                    'success' => false,
                    'status' => $loginStatus,
                    'message' =>
                        'F-Taxi login page failed. HTTP ' .
                        $loginStatus,
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Extract CSRF Token
            |--------------------------------------------------------------------------
            */

            preg_match(
                '/name=["\']_token["\'][^>]*value=["\']([^"\']+)["\']/i',
                $loginBody,
                $matches
            );


            $token =
                $matches[1]
                ?? null;


            if (!$token) {

                /*
                 * Try alternate HTML order.
                 */

                preg_match(
                    '/value=["\']([^"\']+)["\'][^>]*name=["\']_token["\']/i',
                    $loginBody,
                    $matches
                );

                $token =
                    $matches[1]
                    ?? null;
            }


            if (!$token) {

                return [
                    'success' => false,
                    'status' => $loginStatus,
                    'message' =>
                        'F-Taxi CSRF token not found.',
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Submit Login
            |--------------------------------------------------------------------------
            */

            $login = $client->post(
                $this->baseUrl . '/admin/authenticate',
                [
                    'allow_redirects' => false,

                    'headers' => [

                        'User-Agent' =>
                            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) ' .
                            'AppleWebKit/537.36 (KHTML, like Gecko) ' .
                            'Chrome/151.0.0.0 Safari/537.36',

                        'Accept' =>
                            'text/html,application/xhtml+xml,application/json',

                        'Accept-Language' =>
                            'en-US,en;q=0.9',

                        'Referer' =>
                            $this->baseUrl . '/admin/login',

                        'Origin' =>
                            $this->baseUrl,

                        'X-Requested-With' =>
                            'XMLHttpRequest',
                    ],

                    'form_params' => [

                        '_token' =>
                            $token,

                        'user_type' =>
                            env(
                                'FTAXI_USER_TYPE',
                                'Dispatcher'
                            ),

                        'username' =>
                            env('FTAXI_USERNAME'),

                        'password' =>
                            env('FTAXI_PASSWORD'),
                    ],
                ]
            );


            $status =
                $login->getStatusCode();


            $responseBody =
                (string) $login->getBody();


            /*
            |--------------------------------------------------------------------------
            | Check Redirect
            |--------------------------------------------------------------------------
            */

            if (
                $status >= 300 &&
                $status < 400
            ) {

                $location =
                    $login->getHeaderLine('Location');


                /*
                 * Redirect back to login = failed authentication.
                 */

                if (
                    $location &&
                    strpos(
                        $location,
                        '/admin/login'
                    ) !== false
                ) {

                    return [
                        'success' => false,
                        'status' => $status,
                        'message' =>
                            'F-Taxi login failed. Please verify username/password.',
                    ];
                }


                /*
                 * Any other redirect indicates successful login.
                 */

                $this->authenticated = true;


                return [
                    'success' => true,
                    'status' => $status,
                    'message' =>
                        'F-Taxi login successful.',
                    'location' =>
                        $location,
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | HTTP 200 Response
            |--------------------------------------------------------------------------
            */

            if ($status === 200) {

                $body =
                    strtolower(
                        $responseBody
                    );


                /*
                 * If login page is returned again,
                 * authentication failed.
                 */

                if (
                    $this->isLoginHtml(
                        $responseBody
                    )
                ) {

                    return [
                        'success' => false,
                        'status' => $status,
                        'message' =>
                            'F-Taxi login failed. Login page was returned after authentication.',
                    ];
                }


                if (
                    strpos(
                        $body,
                        'invalid credentials'
                    ) !== false
                    ||
                    strpos(
                        $body,
                        'incorrect password'
                    ) !== false
                ) {

                    return [
                        'success' => false,
                        'status' => $status,
                        'message' =>
                            'F-Taxi login failed. Please verify username/password.',
                    ];
                }


                $this->authenticated = true;


                return [
                    'success' => true,
                    'status' => $status,
                    'message' =>
                        'F-Taxi login successful.',
                ];
            }


            return [
                'success' => false,
                'status' => $status,
                'message' =>
                    'F-Taxi login failed. HTTP ' .
                    $status,
            ];


        } catch (\Throwable $e) {

            return [
                'success' => false,
                'status' => 0,
                'message' =>
                    'F-Taxi login error: ' .
                    $e->getMessage(),
            ];
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ENSURE AUTHENTICATED
    |--------------------------------------------------------------------------
    */

    protected function ensureAuthenticated()
    {
        if (
            $this->authenticated
        ) {

            return true;
        }


        $result =
            $this->authenticate();


        if (
            !isset(
                $result['success']
            )
            ||
            !$result['success']
        ) {

            throw new \Exception(
                $result['message']
                ??
                'F-Taxi authentication failed.'
            );
        }


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | CLIENT
    |--------------------------------------------------------------------------
    */

    protected function client()
    {
        $this->ensureAuthenticated();

        return $this->createClient();
    }


    /*
    |--------------------------------------------------------------------------
    | LOGIN HTML CHECK
    |--------------------------------------------------------------------------
    */

    protected function isLoginHtml(
        $body
    ) {

        $body =
            strtolower(
                (string) $body
            );


        return (
            strpos(
                $body,
                '<form'
            ) !== false
            &&
            (
                strpos(
                    $body,
                    'password'
                ) !== false
                ||
                strpos(
                    $body,
                    'username'
                ) !== false
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERIC REQUEST
    |--------------------------------------------------------------------------
    */

    protected function request(
        $url,
        $params = []
    ) {

        try {

            $fullUrl =
                $this->baseUrl .
                $url;


            $response =
                $this->client()
                    ->get(
                        $fullUrl,
                        [
                            'query' => $params,

                            'headers' => [

                                'Accept' =>
                                    'application/json,text/plain,*/*',

                                'X-Requested-With' =>
                                    'XMLHttpRequest',

                                'Referer' =>
                                    $this->baseUrl .
                                    '/admin/login',
                            ],
                        ]
                    );


            $status =
                $response->getStatusCode();


            $body =
                (string) $response->getBody();


            /*
            |--------------------------------------------------------------------------
            | Session Expired / Login Returned
            |--------------------------------------------------------------------------
            */

            if (
                $status === 401
                ||
                $status === 419
                ||
                $this->isLoginHtml(
                    $body
                )
            ) {

                /*
                 * Reset session.
                 */

                $this->authenticated =
                    false;

                $this->cookieJar =
                    new CookieJar();


                /*
                 * Recreate client with new cookie jar.
                 */

                $this->ensureAuthenticated();


                $response =
                    $this->client()
                        ->get(
                            $fullUrl,
                            [
                                'query' => $params,

                                'headers' => [

                                    'Accept' =>
                                        'application/json,text/plain,*/*',

                                    'X-Requested-With' =>
                                        'XMLHttpRequest',

                                    'Referer' =>
                                        $this->baseUrl .
                                        '/admin/login',
                                ],
                            ]
                        );


                $status =
                    $response->getStatusCode();

                $body =
                    (string) $response->getBody();
            }


            /*
            |--------------------------------------------------------------------------
            | HTTP Error
            |--------------------------------------------------------------------------
            */

            if (
                $status < 200 ||
                $status >= 300
            ) {

                return [
                    'success' => false,

                    'status' =>
                        $status,

                    'message' =>
                        'F-Taxi returned HTTP ' .
                        $status,

                    'data' => [],

                    'raw' =>
                        substr(
                            $body,
                            0,
                            2000
                        ),
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Check Login HTML Again
            |--------------------------------------------------------------------------
            */

            if (
                $this->isLoginHtml(
                    $body
                )
            ) {

                return [
                    'success' => false,

                    'status' =>
                        $status,

                    'message' =>
                        'F-Taxi session authentication failed. Login page returned instead of API JSON.',

                    'data' => [],

                    'raw' =>
                        substr(
                            $body,
                            0,
                            2000
                        ),
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Decode JSON
            |--------------------------------------------------------------------------
            */

            $json =
                json_decode(
                    $body,
                    true
                );


            if (
                json_last_error() !==
                JSON_ERROR_NONE
                ||
                !is_array($json)
            ) {

                return [
                    'success' => false,

                    'status' =>
                        $status,

                    'message' =>
                        'F-Taxi returned invalid JSON data.',

                    'data' => [],

                    'raw' =>
                        substr(
                            $body,
                            0,
                            2000
                        ),
                ];
            }


            return [
                'success' => true,

                'status' =>
                    $status,

                'message' =>
                    'Success',

                'data' =>
                    $json,
            ];


        } catch (\Throwable $e) {

            return [
                'success' => false,

                'status' => 0,

                'message' =>
                    'F-Taxi request failed: ' .
                    $e->getMessage(),

                'data' => [],
            ];
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DRIVER PAYOUTS
    |--------------------------------------------------------------------------
    */

    public function getDriverPayouts(
        $start = 0,
        $length = 100
    ) {

        return $this->request(
            '/company/driver_payout/all',
            [
                'draw' =>
                    1,

                'start' =>
                    $start,

                'length' =>
                    $length,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GET ALL PAYOUT RECORDS
    |--------------------------------------------------------------------------
    */

    protected function getAllPayoutRecords()
    {
        $allRecords = [];

        $start = 0;

        $length = 100;

        $maxPages = 100;


        for (
            $page = 0;
            $page < $maxPages;
            $page++
        ) {

            $result =
                $this->getDriverPayouts(
                    $start,
                    $length
                );


            if (
                !$result['success']
            ) {

                return $result;
            }


            $records =
                $result['data']['data']
                ??
                [];


            if (
                !is_array($records)
            ) {

                break;
            }


            foreach (
                $records
                as $record
            ) {

                if (
                    is_array($record)
                ) {

                    $allRecords[] =
                        $record;
                }
            }


            if (
                count($records) <
                $length
            ) {

                break;
            }


            $start +=
                $length;
        }


        return [
            'success' => true,

            'status' => 200,

            'message' =>
                'All F-Taxi payout records fetched.',

            'data' =>
                $allRecords,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | DRIVER PENDING AMOUNTS
    |--------------------------------------------------------------------------
    */

    public function getDriverPendingAmounts()
    {
        $result =
            $this->getAllPayoutRecords();


        if (
            !$result['success']
        ) {

            return $result;
        }


        $records =
            $result['data']
            ?? [];


        $drivers = [];

        $totalPending = 0;


        foreach (
            $records
            as $record
        ) {

            if (
                !is_array($record)
            ) {

                continue;
            }


            $ftaxiDriverId =
                $record['driver_id']
                ??
                $record['ftaxi_driver_id']
                ??
                $record['user_id']
                ??
                null;


            if (
                empty($ftaxiDriverId)
            ) {

                continue;
            }


            /*
             * Official F-Taxi outstanding amount.
             *
             * Example:
             *
             * Pre_Bal = 230
             * P2C     = 166
             * Total   = 396
             */

            $pendingAmount =
                $record['overall_pending_amount']
                ??
                (
                    (float) (
                        $record['previous_balance']
                        ?? 0
                    )
                    +
                    (float) (
                        $record['pending_amt']
                        ?? 0
                    )
                );


            $pendingAmount =
                (float) $pendingAmount;


            $driverKey =
                (string) $ftaxiDriverId;


            if (
                !isset(
                    $drivers[$driverKey]
                )
                ||
                $pendingAmount >
                $drivers[$driverKey]
            ) {

                $drivers[$driverKey] =
                    $pendingAmount;
            }
        }


        foreach (
            $drivers
            as $amount
        ) {

            $totalPending +=
                (float) $amount;
        }


        return [
            'success' => true,

            'status' => 200,

            'message' =>
                'Driver pending amounts fetched successfully.',

            'data' =>
                $drivers,

            'total_pending' =>
                round(
                    $totalPending,
                    2
                ),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | SYNC MONTHLY ACCOUNT
    |--------------------------------------------------------------------------
    */

    public function syncMonthlyAccount(
        $driverId,
        $month,
        $year
    ) {

        $result =
            $this->getAllPayoutRecords();


        if (
            !$result['success']
        ) {

            return $result;
        }


        $records =
            $result['data']
            ?? [];


        $summary = [
            'driver_payout' => 0,
            'driver_amt' => 0,
            'company_amt' => 0,
            'commission' => 0,
            'gst' => 0,
            'business_commission' => 0,
            'pending_amt' => 0,
            'amount_paid' => 0,
            'advance_amount' => 0,
            'toll_fee' => 0,
            'service_charge' => 0,
        ];


        foreach (
            $records
            as $record
        ) {

            if (
                !is_array($record)
            ) {

                continue;
            }


            $ftaxiDriverId =
                $record['driver_id']
                ??
                $record['ftaxi_driver_id']
                ??
                $record['user_id']
                ??
                null;


            if (
                (string) $ftaxiDriverId
                !==
                (string) $driverId
            ) {

                continue;
            }


            $recordMonth =
                trim(
                    (string) (
                        $record['month']
                        ?? ''
                    )
                );


            if (
                strcasecmp(
                    $recordMonth,
                    $month
                ) !== 0
            ) {

                continue;
            }


            $summary['driver_payout'] +=
                (float) (
                    $record['driver_payout']
                    ?? 0
                );


            $summary['driver_amt'] +=
                (float) (
                    $record['driver_amt']
                    ?? 0
                );


            $summary['company_amt'] +=
                (float) (
                    $record['company_amt']
                    ?? 0
                );


            $summary['commission'] +=
                (float) (
                    $record['commission']
                    ?? 0
                );


            $summary['gst'] +=
                (float) (
                    $record['gst']
                    ?? 0
                );


            $summary['business_commission'] +=
                (float) (
                    $record['business_commission']
                    ?? 0
                );


            $summary['pending_amt'] =
                (float) (
                    $record['overall_pending_amount']
                    ??
                    $record['pending_amt']
                    ??
                    0
                );


            $summary['amount_paid'] +=
                (float) (
                    $record['amount_paid']
                    ?? 0
                );


            $summary['advance_amount'] +=
                (float) (
                    $record['advance_amount']
                    ??
                    $record['advance']
                    ??
                    0
                );


            $summary['toll_fee'] +=
                (float) (
                    $record['toll_fee']
                    ?? 0
                );


            $summary['service_charge'] +=
                (float) (
                    $record['service_charge']
                    ?? 0
                );
        }


        foreach (
            $summary
            as $key => $value
        ) {

            $summary[$key] =
                round(
                    (float) $value,
                    2
                );
        }


        FTaxiMonthlyAccount::updateOrCreate(
            [
                'month' =>
                    $month,
            ],
            $summary
        );


        return [
            'success' => true,

            'summary' =>
                $summary,

            'message' =>
                $month .
                ' data saved successfully.',

            'data' => [],
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | JULY 2026 SUMMARY
    |--------------------------------------------------------------------------
    */

    public function getJuly2026Summary()
    {
        return $this->getMonthlyAccount(
            'Jul-26'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AUGUST 2026 SUMMARY
    |--------------------------------------------------------------------------
    */

    public function getAugust2026Summary()
    {
        return $this->getMonthlyAccount(
            'Aug-26'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MONTHLY ACCOUNT
    |--------------------------------------------------------------------------
    */

    protected function getMonthlyAccount(
        $month
    ) {

        $account =
            FTaxiMonthlyAccount::where(
                'month',
                $month
            )->first();


        if ($account) {

            return [
                'success' => true,

                'summary' => [
                    'driver_payout' =>
                        (float) $account->driver_payout,

                    'driver_amt' =>
                        (float) $account->driver_amt,

                    'company_amt' =>
                        (float) $account->company_amt,

                    'commission' =>
                        (float) $account->commission,

                    'gst' =>
                        (float) $account->gst,

                    'business_commission' =>
                        (float) $account->business_commission,

                    'pending_amt' =>
                        (float) $account->pending_amt,

                    'amount_paid' =>
                        (float) $account->amount_paid,

                    'advance_amount' =>
                        (float) $account->advance_amount,

                    'toll_fee' =>
                        (float) $account->toll_fee,

                    'service_charge' =>
                        (float) $account->service_charge,
                ],

                'message' =>
                    $month .
                    ' data loaded successfully.',

                'data' => [],
            ];
        }


        $result =
            $this->getAllPayoutRecords();


        if (
            !$result['success']
        ) {

            return $result;
        }


        $records =
            $result['data']
            ?? [];


        $summary = [
            'driver_payout' => 0,
            'driver_amt' => 0,
            'company_amt' => 0,
            'commission' => 0,
            'gst' => 0,
            'business_commission' => 0,
            'pending_amt' => 0,
            'amount_paid' => 0,
            'advance_amount' => 0,
            'toll_fee' => 0,
            'service_charge' => 0,
        ];


        foreach (
            $records
            as $record
        ) {

            if (
                !is_array($record)
            ) {

                continue;
            }


            $recordMonth =
                trim(
                    (string) (
                        $record['month']
                        ?? ''
                    )
                );


            if (
                strcasecmp(
                    $recordMonth,
                    $month
                ) !== 0
            ) {

                continue;
            }


            $summary['driver_payout'] +=
                (float) (
                    $record['driver_payout']
                    ?? 0
                );


            $summary['driver_amt'] +=
                (float) (
                    $record['driver_amt']
                    ?? 0
                );


            $summary['company_amt'] +=
                (float) (
                    $record['company_amt']
                    ?? 0
                );


            $summary['commission'] +=
                (float) (
                    $record['commission']
                    ?? 0
                );


            $summary['gst'] +=
                (float) (
                    $record['gst']
                    ?? 0
                );


            $summary['business_commission'] +=
                (float) (
                    $record['business_commission']
                    ?? 0
                );


            $summary['pending_amt'] +=
                (float) (
                    $record['overall_pending_amount']
                    ??
                    $record['pending_amt']
                    ??
                    0
                );


            $summary['amount_paid'] +=
                (float) (
                    $record['amount_paid']
                    ?? 0
                );


            $summary['advance_amount'] +=
                (float) (
                    $record['advance_amount']
                    ??
                    $record['advance']
                    ??
                    0
                );


            $summary['toll_fee'] +=
                (float) (
                    $record['toll_fee']
                    ?? 0
                );


            $summary['service_charge'] +=
                (float) (
                    $record['service_charge']
                    ?? 0
                );
        }


        foreach (
            $summary
            as $key => $value
        ) {

            $summary[$key] =
                round(
                    (float) $value,
                    2
                );
        }


        FTaxiMonthlyAccount::updateOrCreate(
            [
                'month' =>
                    $month,
            ],
            $summary
        );


        return [
            'success' => true,

            'summary' =>
                $summary,

            'message' =>
                $month .
                ' data calculated successfully.',

            'data' => [],
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | GET DRIVERS
    |--------------------------------------------------------------------------
    */

    public function getDrivers()
    {
        /*
         * F-Taxi Manage Drivers -> Approved endpoint confirmed from the
         * actual Network response:
         *
         * /company/driver/approved
         *
         * The response contains:
         * - id
         * - first_name / last_name
         * - mobile_number
         * - id_number
         * - status
         * - vehicle_id
         *
         * Vehicle number / make / model are stored in the separate
         * /company/edit_vehicle/{vehicle_id} page, so we fetch that page
         * using the same authenticated session.
         */

        $url = $this->baseUrl . '/company/driver/approved';

        $length = 500;
        $start = 0;

        try {

            $this->ensureAuthenticated();

            $client = $this->client();

            $response = $client->get(
                $url,
                [
                    'query' => [
                        'draw' => 1,
                        'start' => $start,
                        'length' => $length,
                        'search[value]' => '',
                        'search[regex]' => 'false',
                    ],
                    'headers' => [
                        'Accept' =>
                            'application/json, text/plain, */*',

                        'Referer' =>
                            $this->baseUrl .
                            '/company/driver/approved',

                        'X-Requested-With' =>
                            'XMLHttpRequest',
                    ],
                ]
            );

            $status = $response->getStatusCode();

            $body = (string) $response->getBody();

            /*
             |--------------------------------------------------------------------------
             | Session Expired
             |--------------------------------------------------------------------------
             */

            if (
                $status === 401 ||
                $status === 419 ||
                $this->isLoginHtml($body)
            ) {

                $this->authenticated = false;

                $this->cookieJar = new CookieJar();

                $this->ensureAuthenticated();

                $response = $this->client()->get(
                    $url,
                    [
                        'query' => [
                            'draw' => 1,
                            'start' => $start,
                            'length' => $length,
                            'search[value]' => '',
                            'search[regex]' => 'false',
                        ],
                        'headers' => [
                            'Accept' =>
                                'application/json, text/plain, */*',

                            'Referer' =>
                                $this->baseUrl .
                                '/company/driver/approved',

                            'X-Requested-With' =>
                                'XMLHttpRequest',
                        ],
                    ]
                );

                $status = $response->getStatusCode();

                $body = (string) $response->getBody();
            }

            if ($status < 200 || $status >= 300) {
                return [
                    'success' => false,
                    'status' => $status,
                    'message' =>
                        'F-Taxi approved driver list returned HTTP ' .
                        $status,
                    'data' => [],
                    'raw' => substr($body, 0, 2000),
                ];
            }

            $json = json_decode($body, true);

            if (
                !is_array($json) ||
                !isset($json['data']) ||
                !is_array($json['data'])
            ) {
                return [
                    'success' => false,
                    'status' => $status,
                    'message' =>
                        'F-Taxi approved-driver response was invalid.',
                    'data' => [],
                    'raw' => substr($body, 0, 3000),
                ];
            }

            $drivers = [];

            foreach ($json['data'] as $driver) {

                if (!is_array($driver)) {
                    continue;
                }

                $ftaxiDriverId =
                    $driver['id']
                    ??
                    $driver['driver_id']
                    ??
                    null;

                if (empty($ftaxiDriverId)) {
                    continue;
                }

                /*
                 |--------------------------------------------------------------------------
                 | ID Number
                 |--------------------------------------------------------------------------
                 */

                $idNumber = $this->cleanText(
                    $driver['id_number'] ?? ''
                );

                /*
                 |--------------------------------------------------------------------------
                 | Driver Name
                 |--------------------------------------------------------------------------
                 */

                $firstName = $this->cleanText(
                    $driver['first_name'] ?? ''
                );

                $lastName = $this->cleanText(
                    $driver['last_name'] ?? ''
                );

                $name = trim(
                    $firstName . ' ' . $lastName
                );

                if ($name === '') {
                    $name = $this->cleanText(
                        $driver['name'] ?? ''
                    );
                }

                if ($name === '') {
                    $name = 'Unknown Driver';
                }

                /*
                 |--------------------------------------------------------------------------
                 | Phone
                 |--------------------------------------------------------------------------
                 */

                $phone = $this->extractDriverPhone(
                    $driver['mobile_number']
                    ??
                    $driver['phone_number']
                    ??
                    $driver['mobile']
                    ??
                    null
                );

                /*
                 |--------------------------------------------------------------------------
                 | Vehicle
                 |--------------------------------------------------------------------------
                 */

                $vehicleId =
                    $driver['vehicle_id']
                    ??
                    $driver['vehicle']
                    ??
                    null;

                $vehicleDetails = [
                    'vehicle_number' => null,
                    'vehicle_name' => null,
                ];

                if (!empty($vehicleId)) {
                    $vehicleDetails =
                        $this->getVehicleDetails(
                            $vehicleId
                        );
                }

                /*
                 |--------------------------------------------------------------------------
                 | Fallback vehicle fields from driver JSON
                 |--------------------------------------------------------------------------
                 */

                $vehicleNumber =
                    $this->cleanText(
                        $driver['vehicle_number']
                        ??
                        $driver['vehicle_no']
                        ??
                        $driver['vehicle_registration_number']
                        ??
                        $driver['registration_number']
                        ??
                        ''
                    );

                if ($vehicleNumber === '') {
                    $vehicleNumber =
                        $vehicleDetails['vehicle_number'];
                }

                $vehicleName =
                    $this->cleanText(
                        $driver['vehicle_name']
                        ??
                        $driver['vehicle_type']
                        ??
                        ''
                    );

                if ($vehicleName === '') {
                    $vehicleName =
                        $vehicleDetails['vehicle_name'];
                }

                /*
                 |--------------------------------------------------------------------------
                 | Status
                 |--------------------------------------------------------------------------
                 */

                $statusText = $this->cleanText(
                    $driver['status']
                    ??
                    ''
                );

                if ($statusText === '') {
                    $statusText = 'active';
                }

                /*
                 |--------------------------------------------------------------------------
                 | Pending Amount
                 |--------------------------------------------------------------------------
                 */

                $pendingAmount =
                    $driver['overall_pending_amount']
                    ??
                    $driver['pending_amount']
                    ??
                    $driver['pending_amt']
                    ??
                    0;

                $drivers[] = [

                    'ftaxi_driver_id' =>
                        $ftaxiDriverId,

                    'id_number' =>
                        $idNumber !== ''
                            ? $idNumber
                            : null,

                    'name' =>
                        $name,

                    'phone' =>
                        $phone,

                    'vehicle_number' =>
                        $vehicleNumber !== ''
                            ? $vehicleNumber
                            : null,

                    'vehicle_name' =>
                        $vehicleName !== ''
                            ? $vehicleName
                            : null,

                    'pending_amount' =>
                        (float) $pendingAmount,

                    'status' =>
                        strtolower(
                            $statusText
                        ),
                ];
            }

            /*
             |--------------------------------------------------------------------------
             | Sort Drivers
             |--------------------------------------------------------------------------
             */

            usort(
                $drivers,
                function ($a, $b) {

                    return strnatcasecmp(
                        (string) (
                            $a['id_number'] ?? ''
                        ),
                        (string) (
                            $b['id_number'] ?? ''
                        )
                    );
                }
            );

            return [
                'success' => true,

                'status' => $status,

                'message' =>
                    'Approved F-Taxi drivers fetched successfully.',

                'data' => $drivers,

                'recordsTotal' =>
                    $json['recordsTotal']
                    ??
                    $json['iTotalRecords']
                    ??
                    count($drivers),

                'recordsFiltered' =>
                    $json['recordsFiltered']
                    ??
                    $json['iTotalDisplayRecords']
                    ??
                    count($drivers),
            ];

        } catch (\Throwable $e) {

            return [
                'success' => false,

                'status' => 0,

                'message' =>
                    'F-Taxi approved-driver request failed: ' .
                    $e->getMessage(),

                'data' => [],
            ];
        }
    }


    /*
    |--------------------------------------------------------------------------
    | GET VEHICLE DETAILS FROM F-TAXI
    |--------------------------------------------------------------------------
    */

    protected function getVehicleDetails($vehicleId)
    {
        $result = [
            'vehicle_number' => null,
            'vehicle_name' => null,
        ];

        if (empty($vehicleId)) {
            return $result;
        }

        try {

            $url =
                $this->baseUrl .
                '/company/edit_vehicle/' .
                rawurlencode((string) $vehicleId);

            $response = $this->client()->get(
                $url,
                [
                    'headers' => [
                        'Accept' =>
                            'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',

                        'Referer' =>
                            $this->baseUrl .
                            '/company/driver/approved',
                    ],
                ]
            );

            $status = $response->getStatusCode();

            if ($status < 200 || $status >= 300) {
                return $result;
            }

            $html = (string) $response->getBody();

            if ($html === '') {
                return $result;
            }

            /*
             * Vehicle Number
             *
             * The actual F-Taxi page labels the field:
             * "Vehicle Number *"
             *
             * The parser first looks for an input whose name/id contains
             * vehicle + number/registration.
             */

            $vehicleNumber =
                $this->extractInputValueByPattern(
                    $html,
                    [
                        'vehicle_number',
                        'vehicle_no',
                        'registration_number',
                        'registration_no',
                        'vehicle_registration',
                        'vehicle_registration_number',
                        'vehicle_registration_no',
                        'vehicle_reg_no',
                        'registration_number',
                        'registration_no',
                        'registration',
                        'reg_number',
                        'reg_no',
                        'vehicleNumber',
                        'vehicleNo',
                    ]
                );

            /*
             * Fallback: search by the visible "Vehicle Number" label.
             */

            if ($vehicleNumber === null) {

                $vehicleNumber =
                    $this->extractValueNearLabel(
                        $html,
                        'Vehicle Number'
                    );
            }

            if (
                $vehicleNumber !== null &&
                $this->looksLikeVehicleNumber(
                    $vehicleNumber
                )
            ) {
                $result['vehicle_number'] =
                    strtoupper(
                        preg_replace(
                            '/\s+/',
                            '',
                            trim($vehicleNumber)
                        )
                    );
            }

            /*
             * Make + Model
             *
             * The page contains separate Make and Model controls.
             * We combine them for the existing vehicle_name column.
             */

            $make =
                $this->extractSelectedOptionByPattern(
                    $html,
                    [
                        'make',
                        'vehicle_make',
                        'vehiclemaker',
                        'manufacturer',
                    ]
                );

            $model =
                $this->extractSelectedOptionByPattern(
                    $html,
                    [
                        'model',
                        'vehicle_model',
                    ]
                );

            /*
             * Fallback to text inputs.
             */

            if ($make === null) {
                $make =
                    $this->extractInputValueByPattern(
                        $html,
                        [
                            'make',
                            'vehicle_make',
                            'manufacturer',
                        ]
                    );
            }

            if ($model === null) {
                $model =
                    $this->extractInputValueByPattern(
                        $html,
                        [
                            'model',
                            'vehicle_model',
                        ]
                    );
            }

            $vehicleParts = [];

            if (
                $make !== null &&
                trim($make) !== ''
            ) {
                $vehicleParts[] = trim($make);
            }

            if (
                $model !== null &&
                trim($model) !== ''
            ) {
                $vehicleParts[] = trim($model);
            }

            if (!empty($vehicleParts)) {
                $result['vehicle_name'] =
                    implode(
                        ' ',
                        array_unique($vehicleParts)
                    );
            }

            /*
             * Vehicle Type
             *
             * Use checked vehicle-type checkboxes as an additional part
             * of the displayed vehicle name.
             */

            $types =
                $this->extractCheckedVehicleTypes(
                    $html
                );

            if (!empty($types)) {

                $typeText =
                    implode(
                        ', ',
                        array_unique($types)
                    );

                if (
                    empty(
                        $result['vehicle_name']
                    )
                ) {

                    $result['vehicle_name'] =
                        $typeText;

                } else {

                    $result['vehicle_name'] .=
                        ' (' .
                        $typeText .
                        ')';
                }
            }

            return $result;

        } catch (\Throwable $e) {

            return $result;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EXTRACT INPUT VALUE BY NAME / ID
    |--------------------------------------------------------------------------
    */

    protected function extractInputValueByPattern(
        $html,
        array $patterns
    ) {
        $decodedHtml = html_entity_decode(
            (string) $html,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();

        if (@$dom->loadHTML($decodedHtml)) {

            $xpath = new \DOMXPath($dom);
            $inputs = $xpath->query('//input');

            if ($inputs !== false) {

                foreach ($inputs as $input) {

                    $type = strtolower(trim($input->getAttribute('type')));

                    if ($type === 'hidden' || $type === 'checkbox' || $type === 'radio') {
                        continue;
                    }

                    $name = strtolower($input->getAttribute('name'));
                    $id = strtolower($input->getAttribute('id'));
                    $class = strtolower($input->getAttribute('class'));
                    $placeholder = strtolower($input->getAttribute('placeholder'));
                    $value = trim($input->getAttribute('value'));

                    if ($value === '') {
                        continue;
                    }

                    foreach ($patterns as $pattern) {

                        $needle = strtolower($pattern);

                        if (
                            strpos($name, $needle) !== false ||
                            strpos($id, $needle) !== false ||
                            strpos($class, $needle) !== false ||
                            strpos($placeholder, $needle) !== false
                        ) {
                            return $value;
                        }
                    }
                }
            }
        }

        libxml_clear_errors();

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | EXTRACT VALUE NEAR VISIBLE LABEL
    |--------------------------------------------------------------------------
    */

    protected function extractValueNearLabel(
        $html,
        $label
    ) {
        $decodedHtml = html_entity_decode(
            (string) $html,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();

        if (@$dom->loadHTML($decodedHtml)) {

            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query('//label | //div | //span | //td | //p');

            if ($nodes !== false) {

                foreach ($nodes as $node) {

                    $text = trim(preg_replace('/\s+/', ' ', $node->textContent));

                    if ($text === '' || stripos($text, $label) === false) {
                        continue;
                    }

                    $container = $node;

                    for ($level = 0; $level < 7 && $container; $level++) {

                        $inputs = $xpath->query('.//input', $container);

                        if ($inputs !== false) {

                            foreach ($inputs as $input) {

                                $type = strtolower(trim($input->getAttribute('type')));

                                if ($type === 'hidden' || $type === 'checkbox' || $type === 'radio') {
                                    continue;
                                }

                                $value = trim($input->getAttribute('value'));

                                if ($value !== '') {
                                    return $value;
                                }
                            }
                        }

                        $container = $container->parentNode;
                    }
                }
            }
        }

        libxml_clear_errors();

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | EXTRACT SELECTED OPTION
    |--------------------------------------------------------------------------
    */

    protected function extractSelectedOptionByPattern(
        $html,
        array $patterns
    ) {
        foreach ($patterns as $pattern) {

            $patternQuoted =
                preg_quote(
                    $pattern,
                    '/'
                );

            if (
                preg_match(
                    '/<select\b[^>]*(?:name|id)=["\'][^"\']*' .
                    $patternQuoted .
                    '[^"\']*["\'][^>]*>[\s\S]*?' .
                    '<option\b[^>]*selected[^>]*>(.*?)<\/option>/i',
                    $html,
                    $matches
                )
            ) {

                $value =
                    $this->cleanText(
                        $matches[1]
                    );

                if (
                    $value !== ''
                ) {
                    return $value;
                }
            }
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | EXTRACT CHECKED VEHICLE TYPES
    |--------------------------------------------------------------------------
    */

    protected function extractCheckedVehicleTypes(
        $html
    ) {
        $types = [];

        if (
            preg_match_all(
                '/<input\b[^>]*(?:name|id)=["\'][^"\']*vehicle[^"\']*type[^"\']*["\'][^>]*checked[^>]*>/i',
                $html,
                $matches
            )
        ) {

            foreach (
                $matches[0]
                as $input
            ) {

                /*
                 * Prefer the nearby label text.
                 */

                if (
                    preg_match(
                        '/<label\b[^>]*>\s*([^<]+?)\s*<\/label>/i',
                        $input,
                        $labelMatch
                    )
                ) {

                    $type =
                        $this->cleanText(
                            $labelMatch[1]
                        );

                    if (
                        $type !== ''
                    ) {
                        $types[] = $type;
                    }
                }
            }
        }

        /*
         * A simpler fallback: parse checked checkbox values.
         */

        if (
            empty($types) &&
            preg_match_all(
                '/<input\b[^>]*type=["\']checkbox["\'][^>]*checked[^>]*value=["\']([^"\']+)["\'][^>]*>/i',
                $html,
                $matches
            )
        ) {

            foreach (
                $matches[1]
                as $value
            ) {

                $value =
                    $this->cleanText(
                        $value
                    );

                if (
                    $value !== ''
                ) {
                    $types[] = $value;
                }
            }
        }

        return array_values(
            array_unique(
                $types
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VEHICLE NUMBER CHECK
    |--------------------------------------------------------------------------
    */

    protected function looksLikeVehicleNumber(
        $value
    ) {
        $value =
            strtoupper(
                trim(
                    (string) $value
                )
            );

        if (
            $value === '' ||
            $value === '+' ||
            $value === '-'
        ) {
            return false;
        }

        /*
         * Indian-style registration numbers.
         */

        if (
            preg_match(
                '/^[A-Z]{2}[0-9]{1,2}[A-Z]{0,3}[0-9]{1,4}$/',
                preg_replace(
                    '/[\s-]+/',
                    '',
                    $value
                )
            )
        ) {
            return true;
        }

        /*
         * Also accept non-empty values from the F-Taxi form.
         */

        return strlen($value) >= 4;
    }


    /*
    |--------------------------------------------------------------------------
    | CLEAN TEXT
    |--------------------------------------------------------------------------
    */

    protected function cleanText(
        $value
    ) {

        if (
            $value === null
        ) {

            return '';
        }


        $value =
            html_entity_decode(
                (string) $value,
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            );


        $value =
            strip_tags(
                $value
            );


        $value =
            preg_replace(
                '/\s+/',
                ' ',
                $value
            );


        return trim(
            $value
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXTRACT DRIVER PHONE
    |--------------------------------------------------------------------------
    */

    protected function extractDriverPhone(
        $value
    ) {

        if (
            $value === null
        ) {

            return null;
        }


        $value =
            html_entity_decode(
                (string) $value,
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            );


        $value =
            strip_tags(
                $value
            );


        /*
         * Example:
         *
         * Driver : 9345836548
         * Owner  : 7806956408
         */

        if (
            preg_match(
                '/Driver\s*:\s*([0-9]{10,15})/i',
                $value,
                $matches
            )
        ) {

            return $matches[1];
        }


        /*
         * Otherwise extract first valid number.
         */

        if (
            preg_match(
                '/[0-9]{10,15}/',
                $value,
                $matches
            )
        ) {

            return $matches[0];
        }


        return null;
    }
}