<?php

return [
  'baseURL' => env('RPDPYMNT_API_BASE_URL'),
  'endpoints' => [
    'merchant_login' => env('RPDPYMNT_API_MERCHANT_LOGIN_ENDPOINT_URL', 'merchant/user/login'),
    'transaction_report' => env('RPDPYMNT_API_TRANSACTION_REPORT_ENDPOINT_URL', 'transactions/report'),
    'transaction_query' => env('RPDPYMNT_API_TRANSACTION_QUERY_ENDPOINT_URL', 'transaction/list'),
    'get_transaction' => env('RPDPYMNT_API_GET_TRANSACTION_ENDPOINT_URL', 'transaction'),
  ],
  'testAccount' => [
    'email' => env('RPDPYMNT_API_TEST_ACCOUNT_EMAIL'),
    'password' => env('RPDPYMNT_API_TEST_ACCOUNT_PASSWORD'),
  ],
];