<?php

namespace App\Http\Controllers;

class BaseController extends Controller {
  public $vData = [];

  public function __construct() {
    $this->vData['_v'] = '0.0.1';
  }
}