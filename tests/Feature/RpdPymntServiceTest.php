<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Services\RpdPymntService;
use Illuminate\Support\Facades\Http;

class RpdPymntServiceTest extends TestCase {
    private function _getTestAccessToken() {
        $response = Http::post(config('rpdpymnt.baseURL').config('rpdpymnt.endpoints.merchant_login'), [
            'email' => config('rpdpymnt.testAccount.email'),
            'password' => config('rpdpymnt.testAccount.password'),
        ]);

        if($response->ok()) {
            $response = $response->json();

            if(isset($response['token']) && $response['token']) {
                return $response['token'];
            }
        }

        return;
    }

    public function test_rpdpymnt_prevents_access_to_getTransactions_without_a_valid_access_token() {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('[RpdPymnt] Wrong number of segments');

        $service = new RpdPymntService('an-invalid-access-token');

        $service->getTransactions();
    }

    public function test_rpdpymnt_prevents_access_to_getTransactions_with_an_expired_access_token() {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('[RpdPymnt] Token Expired');

        $expiredToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJtZXJjaGFudFVzZXJJZCI6NTMsInJvbGUiOiJ1c2VyIiwibWVyY2hhbnRJZCI6Mywic3ViTWVyY2hhbnRJZHMiOlszLDc0LDkzLDExOTEsMTI5NSwxMTEsMTM3LDEzOCwxNDIsMTQ1LDE0NiwxNTMsMzM0LDE3NSwxODQsMjIwLDIyMSwyMjIsMjIzLDI5NCwzMjIsMzIzLDMyNywzMjksMzMwLDM0OSwzOTAsMzkxLDQ1NSw0NTYsNDc5LDQ4OCw1NjMsMTE0OSw1NzAsMTEzOCwxMTU2LDExNTcsMTE1OCwxMTc5LDEyOTMsMTI5NCwxMzA2LDEzMDcsMTMyNCwxMzMxXSwidGltZXN0YW1wIjoxNjY2NTQ3ODYyfQ.oFCB7H3RRJRnklSVUXozraX45RepCYZ3TGxOme-e5KE';
        $service = new RpdPymntService($expiredToken);

        $service->getTransactions();
    }

    public function test_getTransactions_returns_a_list_of_transactions_without_params() {
        $service = new RpdPymntService($this->_getTestAccessToken());
  
        $response = $service->getTransactions();
        $this->assertTrue($response['result']);
    }

    public function test_getTransactions_returns_a_list_of_transactions_with_a_given_date_range() {
        $service = new RpdPymntService($this->_getTestAccessToken());

        $nowDT = new \DateTime;

        $params = [];
        $params['fromDate'] = '2015-01-01';
        $params['toDate'] = $nowDT->format('Y-m-d');
  
        $response = $service->getTransactions($params);
        $this->assertTrue($response['result']);
    }

    public function test_getTransactions_returns_a_list_of_transactions_with_a_given_status() {
        $service = new RpdPymntService($this->_getTestAccessToken());

        $nowDT = new \DateTime;

        $params = [];
        $params['status'] = 'APPROVED';
  
        $response = $service->getTransactions($params);
        $this->assertTrue($response['result']);
    }
    
    public function test_rpdpymnt_prevents_access_to_getTransaction_without_a_valid_access_token() {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('[RpdPymnt] Wrong number of segments');

        $service = new RpdPymntService('an-invalid-access-token');

        $service->getTransaction('some-transactionId');
    }

    public function test_rpdpymnt_prevents_access_to_getTransaction_with_an_expired_access_token() {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('[RpdPymnt] Token Expired');

        $expiredToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJtZXJjaGFudFVzZXJJZCI6NTMsInJvbGUiOiJ1c2VyIiwibWVyY2hhbnRJZCI6Mywic3ViTWVyY2hhbnRJZHMiOlszLDc0LDkzLDExOTEsMTI5NSwxMTEsMTM3LDEzOCwxNDIsMTQ1LDE0NiwxNTMsMzM0LDE3NSwxODQsMjIwLDIyMSwyMjIsMjIzLDI5NCwzMjIsMzIzLDMyNywzMjksMzMwLDM0OSwzOTAsMzkxLDQ1NSw0NTYsNDc5LDQ4OCw1NjMsMTE0OSw1NzAsMTEzOCwxMTU2LDExNTcsMTE1OCwxMTc5LDEyOTMsMTI5NCwxMzA2LDEzMDcsMTMyNCwxMzMxXSwidGltZXN0YW1wIjoxNjY2NTQ3ODYyfQ.oFCB7H3RRJRnklSVUXozraX45RepCYZ3TGxOme-e5KE';
        $service = new RpdPymntService($expiredToken);

        $service->getTransaction('some-transactionId');
    }

    public function test_getTransaction_does_not_accept_null_value_for_transactionId() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid argument for $transactionId');

        $service = new RpdPymntService($this->_getTestAccessToken());
  
        $service->getTransaction(null);
    }

    public function test_getTransaction_does_not_accept_blank_value_for_transactionId() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid argument for $transactionId');

        $service = new RpdPymntService($this->_getTestAccessToken());
  
        $service->getTransaction('');
    }

    public function test_getTransaction_does_not_an_invalid_transactionId() {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('[RpdPymnt] The merchant has no permission to perform this operation!');
        $service = new RpdPymntService($this->_getTestAccessToken());
  
        $service->getTransaction('an-invalid-transaction-id');
    }

    public function test_getTransaction_returns_transaction_details_for_a_given_transactionId() {
        $service = new RpdPymntService($this->_getTestAccessToken());
  
        $response = $service->getTransaction('1030245-1606174013-1307');
        $this->assertTrue($response['result']);
    }
}
