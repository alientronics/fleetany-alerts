<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\AlertsRepository;

class AlertsController extends Controller
{
    public function get($entity_key, $entity_id)
    {
        $alertsRepository = new AlertsRepository();
        return $alertsRepository->get($entity_key, $entity_id);
    }
    
    public function getVehicleAlerts($vehicle_id, $company_id)
    {
        $alertsRepository = new AlertsRepository();
        return $alertsRepository->getVehicleAlerts($vehicle_id, $company_id);
    }
    
    public function getVehicleAlertTypeReport($vehicle_id, $alert_type, $company_id)
    {
        $alertsRepository = new AlertsRepository();
        return $alertsRepository->getVehicleAlertTypeReport($vehicle_id, $alert_type, $company_id);
    }
    
    public function create(Request $request)
    {
        $alertsRepository = new AlertsRepository();
        return response()->json($alertsRepository->create($request));
    }
}
