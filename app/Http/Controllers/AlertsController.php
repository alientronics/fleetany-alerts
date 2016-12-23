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
    
    public function getAlerts($entity_key, $entity_id, $company_id)
    {
        $alertsRepository = new AlertsRepository();
        return $alertsRepository->getAlerts($entity_key, $entity_id, $company_id);
    }
    
    public function getAlertTypeReport($entity_key, $entity_id, $alert_type, $company_id)
    {
        $alertsRepository = new AlertsRepository();
        return $alertsRepository->getAlertTypeReport($entity_key, $entity_id, $alert_type, $company_id);
    }
    
    public function create(Request $request)
    {
        $alertsRepository = new AlertsRepository();
        return response()->json($alertsRepository->create($request));
    }
    
    public function sendFakeEmail($email, $option = 'view')
    {
        $alertsRepository = new AlertsRepository();
        return $alertsRepository->sendFakeEmail($email, $option);
    }
}
