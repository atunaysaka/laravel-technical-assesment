<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RpdPymntService {
  private $accessToken;

  public function __construct($accessToken) {
    $this->accessToken = $accessToken;
  }

  public static function login($email, $password) {
    if(!$email) throw new \InvalidArgumentException('Invalid argument for $email');
    if(!$password) throw new \InvalidArgumentException('Invalid argument for $password');

    $response = Http::post(config('rpdpymnt.baseURL').config('rpdpymnt.endpoints.merchant_login'), [
      'email' => $email,
      'password' => $password,
    ]);

    if($response->clientError() || $response->serverError() || (isset($response['status']) && $response['status'] == 'DECLINED')) {
      $response = $response->json();
      throw new \Exception('[RpdPymnt] '.$response['message']);
    }

    if($response->ok()) {
      $response = $response->json();

      if(isset($response['status']) && $response['status'] == 'APPROVED' && isset($response['token']) && $response['token']) {
        return [
          'result' => true,
          'token' => $response['token']
        ];
      }
    }

    return ['result' => false];
  }

  public function getTransactions($params = []) {
    $response = Http::withHeaders([
      'Authorization' => $this->accessToken
    ])->post(config('rpdpymnt.baseURL').config('rpdpymnt.endpoints.transaction_query'), $params);

    if($response->clientError() || $response->serverError() || (isset($response['status']) && $response['status'] == 'DECLINED')) {
      $response = $response->json();
      throw new \Exception('[RpdPymnt] '.$response['message']);
    }

    if($response->ok()) {
      $response = $response->json();

      return [
        'result' => true,
        'transactions' => $response
      ];
    }

    return ['result' => false];
  }

  public function getTransaction($transactionId) {
    if(!$transactionId) throw new \InvalidArgumentException('Invalid argument for $transactionId');

    $response = Http::withHeaders([
      'Authorization' => $this->accessToken
    ])->post(config('rpdpymnt.baseURL').config('rpdpymnt.endpoints.get_transaction'), [
      'transactionId' => $transactionId
    ]);

    if($response->clientError() || $response->serverError() || (isset($response['status']) && $response['status'] == 'DECLINED')) {
      $response = $response->json();
      throw new \Exception('[RpdPymnt] '.$response['message']);
    }

    if($response->ok()) {
      $response = $response->json();

      return [
        'result' => true,
        'transaction' => $response
      ];
    }

    return ['result' => false];
  }
}