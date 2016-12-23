<?php
namespace App\Repositories;

use Illuminate\Http\Request;
use App\Entities\Alerts;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Entities\AlertsTypes;

class AlertsRepository
{
    public function get($entity_key, $entity_id)
    {
        $alerts = Alerts::where('entity_id', $entity_id)->where('entity_key', $entity_key)->get();
    
        return response()->json($alerts);
    }
    
    public function getAlerts($entity_key, $entity_id, $company_id)
    {
        switch ($entity_key) {
            case "vehicle":
                return $this->getVehicleAlerts($entity_id, $company_id);
            case "tire":
                return $this->getTireAlerts($entity_id, $company_id);
        }
    }
    
    private function getVehicleAlerts($vehicle_id, $company_id)
    {
        $alertsTypes = AlertsTypes::orderBy('id', 'asc')->get();

        if (!empty($alertsTypes)) {
            foreach ($alertsTypes as $index => $alertType) {
                $alertsTypes[$index]['quantity'] = Alerts::where('alert_type_id', $alertType->id)
                            ->where('description', 'like', '%vehicle_id":'.$vehicle_id.'%')
                            ->where('company_id', $company_id)
                            ->count();
            }
        }
        
        return response()->json($alertsTypes);
    }
    
    private function getTireAlerts($entity_id, $company_id)
    {
        $alertsTypes = AlertsTypes::orderBy('id', 'asc')->get();

        if (!empty($alertsTypes)) {
            foreach ($alertsTypes as $index => $alertType) {
                $alertsTypes[$index]['quantity'] = Alerts::where('alert_type_id', $alertType->id)
                            ->where('entity_key', "tire")
                            ->where('entity_id', $entity_id)
                            ->where('company_id', $company_id)
                            ->count();
            }
        }
        
        return response()->json($alertsTypes);
    }
    
    public function getAlertTypeReport($entity_key, $entity_id, $alert_type, $company_id)
    {
        switch ($entity_key) {
            case "vehicle":
                return $this->getVehicleAlertTypeReport($entity_id, $alert_type, $company_id);
            case "tire":
                return $this->getTireAlertTypeReport($entity_id, $alert_type, $company_id);
        }
    }
    
    private function getVehicleAlertTypeReport($vehicle_id, $alert_type, $company_id)
    {
        $alerts = Alerts::where('alert_type_id', $alert_type)
                            ->where('description', 'like', '%vehicle_id":'.$vehicle_id.'%')
                            ->where('company_id', $company_id)
                            ->get();

        return response()->json($alerts);
    }
    
    private function getTireAlertTypeReport($entity_id, $alert_type, $company_id)
    {
        $alerts = Alerts::where('alert_type_id', $alert_type)
                            ->where('entity_key', "tire")
                            ->where('entity_id', $entity_id)
                            ->where('company_id', $company_id)
                            ->get();

        return response()->json($alerts);
    }
    
    public function create(Request $request)
    {
        try {
            $data = $request->all();

            $message = json_decode($data['message']);
            $alertType = AlertsTypes::where('name', $message->id)->first();
            $emails = json_decode($data['emails']);
            
            $alert = [
                'company_id' => $data['company_id'],
                'alert_type_id' => $alertType->id,
                'entity_key' => $data['entity_key'],
                'entity_id' => $data['entity_id'],
                'destination' => $data['emails'],
                'description' => $data['message'],
                'status' => 1,
            ];
         
            if ($this->sendMail($data['company_id'])) {
                try {
                    Mail::send($alertType->resource, ['alarm' => $message], function ($m) use ($emails, $data) {
                        $m->from(env('MAIL_SENDER'), 'fleetany sender');
                        $m->to($emails)->subject($data['subject'], json_decode($data['subject_params']));
                    });
                } catch (\Exception $e) {
                    $alert['status'] = 0;
                }
                
                $newAlert = new Alerts($alert);
                $newAlert->save();
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
    }
    
    public function sendFakeEmail($email, $option)
    {
        try {
            $alarm = new \stdClass();
            $alarm->vehicle_fleet = 'vehicle_fleet';
            $alarm->vehicle_plate = 'vehicle_plate';
            $alarm->vehicle_driver = 'vehicle_driver';
            $alarm->tire_number = 'tire_number';
            $alarm->type = 'type';
            $alarm->description = 'description';
            $alarm->vehicle_latitude = 'vehicle_latitude';
            $alarm->vehicle_longitude = 'vehicle_longitude';
            $alarm->vehicle_id = 'vehicle_id';
            $emails[] = $email;
            
            if (env('APP_DEBUG')) {
                if ($option == 'mail') {
                    Mail::send('mail-alert', ['alarm' => $alarm], function ($m) use ($emails) {
                        $m->from(env('MAIL_SENDER'), 'fleetany sender');
                        $m->to($emails)->subject('fleetany-alerts mail test');
                    });

                    return response()->json(['success' => true]);
                } else {
                    return view("mail-alert", compact('alarm'));
                }
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['failed' => $e->getMessage()]);
        }
    }
    
    private function sendMail($company_id)
    {
        $lastAlert = Alerts::where('company_id', $company_id)
                        ->orderBy('created_at', 'desc')
                        ->first();
      
        $sendMail = false;
        if (empty($lastAlert)) {
            $sendMail = true;
        } else {
            $diffHours = sprintf('%2d', (strtotime(date("Y-m-d H:i:s")) -
                strtotime($lastAlert->created_at)) / 3600);
        
            if ($diffHours >= 12) {
                $sendMail = true;
            }
        }
        
        return $sendMail;
    }
}
