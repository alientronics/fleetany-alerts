<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Entities\Alerts;

class AlertsTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }
    
    public function testPingApi()
    {
        $this->get('/');
    
        $this->assertEquals(
            $this->response->getContent(),
            $this->app->version()
        );
    }
    
    public function testAlertsGetAllSuccess()
    {
        $alert = factory('App\Entities\Alerts')->create();
        
        $this->get('/api/v1/alerts/tire/1?api_token='.env('APP_TOKEN'));
        $this->assertEquals($this->response->status(), 200);
    }
    
    public function testAlertsPostFail()
    {
        $this->post('/api/v1/alert', ['emails' => json_encode($emails),
                'company_id' => 1,
                'names' => json_encode($names),
                'subject' => 'Teste Assunto Email',
                'subject_params' => json_encode($subject_params),
                'message' => '{"vehicle_fleet":"123","vehicle_plate":null,"vehicle_driver":"Company","tire_number":"0","type":"mails.Pressure","description":"mails.HighPressure","id":"High Pressure","vehicle_latitude":"51.1000000","vehicle_longitude":"30.0500000","vehicle_id":1}',
                'entity_key' => 'tire',
                'entity_id' => 11,
            ]);

        $this->assertEquals($this->response->status(), 401);
    }

    public function testAlertsPostSuccess()
    {
        $emails = [];
        $names = [];
        $emails[] = "admin@alientronics.com.br";
        $emails[] = "teste@alientronics.com.br"; 
        $names[] = "Administrator";
        $names[] = "Nome Usuario Executive";
        $subject_params = [];
        
        $this->post('/api/v1/alert', ['api_token' => env('APP_TOKEN'),
                'emails' => json_encode($emails),
                'company_id' => 1,
                'names' => json_encode($names),
                'subject' => 'Teste Assunto Email',
                'subject_params' => json_encode($subject_params),
                'message' => '{"vehicle_fleet":"123","vehicle_plate":null,"vehicle_driver":"Company","tire_number":"0","type":"mails.Pressure","description":"mails.HighPressure","id":"High Pressure","vehicle_latitude":"51.1000000","vehicle_longitude":"30.0500000","vehicle_id":1}',
                'entity_key' => 'tire',
                'entity_id' => 11,
            ])
            ->assertResponseStatus(200);
    }
}
