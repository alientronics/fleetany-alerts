<?php

use Illuminate\Database\Seeder;
use App\Entities\AlertsTypes;

class AlertsTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('alerts_types')->delete();
        AlertsTypes::forceCreate(
                [  'name' => 'Low Pressure',
                   'resource' => 'mail-alert'
                ]
            );
        
        AlertsTypes::forceCreate(
                [  'name' => 'High Pressure',
                   'resource' => 'mail-alert'
                ]
            );
        
        AlertsTypes::forceCreate(
                [  'name' => 'High Temperature',
                   'resource' => 'mail-alert'
                ]
            );
        
        AlertsTypes::forceCreate(
                [  'name' => 'Valve Leak',
                   'resource' => 'mail-alert'
                ]
            );
        
        AlertsTypes::forceCreate(
                [  'name' => 'Stolen Tire/No Signal',
                   'resource' => 'mail-alert'
                ]
            );
        
        AlertsTypes::forceCreate(
                [  'name' => 'Maintenance',
                   'resource' => 'mail-alert'
                ]
            );
        
    }
}
