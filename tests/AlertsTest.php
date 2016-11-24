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
        
        $this->get('/api/v1/alerts/tire/1', ['api_token' => env('APP_TOKEN')]);
        $this->assertEquals($this->response->status(), 200);
    }
    
    public function testAlertsPostFail()
    {
        $this->post('/api/v1/alert', ['company_id' => 1,
            'tiresensor_id' => 1,
            'vehicle_id' => 1
        ]);

        $this->assertEquals($this->response->status(), 401);
    }

    public function testAlertsPostSuccess()
    {
        $this->post('/api/v1/alert', ['api_token' => env('APP_TOKEN'),
                'company_id' => 1,
                'tiresensor_id' => 1,
                'vehicle_id' => 1
            ])
            ->assertResponseStatus(200);
    }
}
