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
  
    public function create(Request $request)
    {
        $alertsRepository = new AlertsRepository();
        return response()->json($alertsRepository->create($request));
    }
}
