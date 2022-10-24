<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Validator;
use Log;

use App\Services\RpdPymntService;

class PageController extends BaseController {
  public function login(Request $request) {
    if(Cookie::get('accessToken')) {
      return redirect()->route('home');
    }

    if($request->isMethod('post')) {
      $rules = [
        'email' => 'required|email',
        'password' => 'required'
      ];

      $validator = Validator::make($request->all(), $rules);
      $validator->validate();

      $email = $request->input('email');
      $password = $request->input('password');

      try {
        $response = RpdPymntService::login($email, $password);
        if(isset($response['result']) && $response['result'] && isset($response['token']) && $response['token']) {
          Cookie::queue('accessToken', $response['token'], 10);
          return redirect()->route('home');
        }
      } catch(\Exception $exception) {
        Log::debug($exception->getMessage());
      }

      $validator->after(function ($validator) {
        $validator->errors()->add(
          'login', 'Invalid email address or password.'
        );
      })->validate();
    }

    return view('pages.login', $this->vData);
  }

  public function home(Request $request) {
    return view('pages.home', $this->vData);
  }

  public function reports(Request $request) {
    return view('pages.reports', $this->vData);
  }

  public function transactions(Request $request) {
    // Defaults
    $fromDateDT = \DateTime::createFromFormat('d/m/Y H:i:s', '01/01/2018 00:00:00');
    $toDateDT = new \DateTime;

    if($dateRange = $request->input('dates')) {
      $dates = explode(' | ', $dateRange);
      
      $fromDate = $dates[0];
      $toDate = $dates[1];

      $fromDateDT = \DateTime::createFromFormat('d/m/Y H:i:s', $fromDate.' 00:00:00');
      $toDateDT = \DateTime::createFromFormat('d/m/Y H:i:s', $toDate.' 00:00:00');
    }

    $params = [];
    $params['page'] = $request->input('page', 1);
    if($fromDateDT) $params['fromDate'] = $fromDateDT->format('Y-m-d');
    if($toDateDT) $params['toDate'] = $toDateDT->format('Y-m-d');
    if($request->input('operation')) $params['operation'] = $request->input('operation');
    if($request->input('paymentMethod')) $params['paymentMethod'] = $request->input('paymentMethod');
    if($request->input('errorCode')) $params['errorCode'] = $request->input('errorCode');
    if($request->input('filterField')) $params['filterField'] = $request->input('filterField');
    if($request->input('filterValue')) $params['filterValue'] = $request->input('filterValue');
    if($request->input('status')) $params['status'] = $request->input('status');
    
    try {
      $service = new RpdPymntService(Cookie::get('accessToken'));
      $response = $service->getTransactions($params);

      $this->vData['transactions'] = $response['transactions'];
    } catch (\Exception $exception) {
      $this->vData['error'] = $exception->getMessage();
    }

    $filterOptions = [
      'operation' => [
        'DIRECT',
        'REFUND',
        '3D',
        '3DAUTH',
        'STORED'
      ],
      'paymentMethod' => [
        'CREDITCARD',
        'CUP',
        'IDEAL',
        'GIROPAY',
        'MISTERCASH',
        'STORED',
        'PAYTOCARD',
        'CEPBANK',
        'CITADEL'
      ],
      'errorCode' => [
        'Do not honor',
        'Invalid Transaction',
        'Invalid Card',
        'Not sufficient funds',
        'Incorrect PIN',
        'Invalid country association',
        'Currency not allowed',
        '3-D Secure Transport Error',
        'Transaction not permitted to cardholder'
      ],
      'filterField' => [
        'Transaction UUID',
        'Customer Email',
        'Reference No',
        'Custom Data',
        'Card PAN'
      ],
      'status' => [
        'APPROVED',
        'WAITING',
        'DECLINED',
        'ERROR' 
      ]
    ];

    $this->vData['filterOptions'] = $filterOptions;

    $initialValues = [
      'fromDate' => $fromDateDT->format('d/m/Y'),
      'toDate' => $toDateDT->format('d/m/Y'),
      'operation' => $request->input('operation'),
      'paymentMethod' => $request->input('paymentMethod'),
      'errorCode' => $request->input('errorCode'),
      'filterField' => $request->input('filterField'),
      'filterValue' => $request->input('filterValue'),
      'status' => $request->input('status'),
    ];

    $this->vData['initialValues'] = $initialValues;

    return view('pages.transactions', $this->vData);
  }

  public function transactionById(Request $request, $transactionId) {
    try {
      $service = new RpdPymntService(Cookie::get('accessToken'));
      $response = $service->getTransaction($transactionId);
      
      return ['result' => true, 'transaction' => $response['transaction']];
    } catch (\Exception $exception) {
      return ['result' => false, 'error' => $exception->getMessage()];
    }

    return ['result' => false];
  }
}