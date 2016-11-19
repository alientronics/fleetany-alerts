<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\AlertsRepository;

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
        return $alertsRepository->create($request);
    }
}
