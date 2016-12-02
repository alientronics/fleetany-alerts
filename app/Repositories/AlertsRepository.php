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
    
    public function getVehicleAlerts($vehicle_id, $company_id)
    {
        $alertsTypes = AlertsTypes::orderBy('id', 'asc')->get();

        if(!empty($alertsTypes))
        {
            foreach($alertsTypes as $index => $alertType)
            {
                $alertsTypes[$index]['quantity'] = Alerts::where('alert_type_id', $alertType->id)
                            ->where('description', 'like', '%vehicle_id":'.$vehicle_id.'%')
                            ->where('company_id', $company_id)
                            ->count();
            }
        } 
        
        return response()->json($alertsTypes);
    }
    
    public function getVehicleAlertTypeReport($vehicle_id, $alert_type, $company_id)
    {
        $alerts = Alerts::where('alert_type_id', $alert_type)
                            ->where('description', 'like', '%vehicle_id":'.$vehicle_id.'%')
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
