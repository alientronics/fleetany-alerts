<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Entities\Alerts;

class AlertsTest extends TestCase
{
    public function testTireSensorPostFail()
    {
        $this->post('/api/v1/alerts', ['company_id' => 1, 
            'tiresensor_id' => 1,
            'vehicle_id' => 1
        ]);

        $this->assertEquals($this->response->status(), 401);

    }

    public function testTireSensorPostSuccess()
    {
        $company = factory('App\Company')->create();

        $this->actingAs($company)
            ->post('/api/v1/alerts', ['api_token' => env('APP_TOKEN'), 
                'company_id' => 1, 
                'tiresensor_id' => 1,
                'vehicle_id' => 1
            ])
            ->assertResponseStatus(200);
    }
}
