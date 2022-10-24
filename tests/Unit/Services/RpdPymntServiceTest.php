<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

use App\Services\RpdPymntService;

class RpdPymntServiceTest extends TestCase {
    public function test_login_does_not_accept_null_value_for_email() {
      $this->expectException(\InvalidArgumentException::class);
      $this->expectExceptionMessage('Invalid argument for $email');

      RpdPymntService::login(null, 'some-password');
    }

    public function test_login_does_not_accept_blank_value_for_email() {
      $this->expectException(\InvalidArgumentException::class);
      $this->expectExceptionMessage('Invalid argument for $email');

      RpdPymntService::login('', 'some-password');
    }

    public function test_login_does_not_accept_null_value_for_password() {
      $this->expectException(\InvalidArgumentException::class);
      $this->expectExceptionMessage('Invalid argument for $password');

      RpdPymntService::login('user@test.com', null);
    }

    public function test_login_does_not_accept_blank_value_for_password() {
      $this->expectException(\InvalidArgumentException::class);
      $this->expectExceptionMessage('Invalid argument for $password');

      RpdPymntService::login('user@test.com', '');
    }
}
