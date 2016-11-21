<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\AlertsRepository;
use App\Entities\TireSensor;
use App\Company;

class AlertsController extends Controller
{
    public function get($idPart)
    {
        $alertsRepository = new AlertsRepository();
        return $alertsRepository->get($idPart);
    }
  
    public function create(Request $request)
    {
        $alertsRepository = new AlertsRepository();
        return response()->json($alertsRepository->create($request));
    }
  
    public function getAlertType($company_id, $tiresensor_id)
    {
        $alertsRepository = new AlertsRepository();
        
        $tireSensor = TireSensor::find($tiresensor_id);
        $company = Company::where('id', $company_id)->first();
        $company->delta_pressure = $company->delta_pressure / 100;
        
        $ideal_pressure = $alertsRepository->calculateIdealPressure($tireSensor, $company);
        
        return response()->json($alertsRepository->getAlertType(
            $company,
            $tireSensor,
            $ideal_pressure
        ));
    }
}
