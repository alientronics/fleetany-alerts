<?php
namespace App\Repositories;

use Illuminate\Http\Request;
use App\Entities\Alerts;
use Log;
use Illuminate\Support\Facades\Mail;
use App\Entities\AlertsTypes;

class AlertsRepository
{
    public function get($entity_key, $entity_id)
    {
        $alerts = Alerts::where('entity_id', $entity_id)->where('entity_key', $entity_key)->get();
    
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
                'alert_type_id' => $alertType->id,
                'entity_key' => $data['entity_key'],
                'entity_id' => $data['entity_id'],
                'destination' => $data['emails'],
                'description' => $data['message'],
                'status' => 1,
            ];
         
            if ($this->sendMail($alert)) {
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
    
    private function sendMail($alert = null)
    {
        return true;
        
        $sendMail = false;
        if (empty($alert->created_at) || $alert->created_at == '0000-00-00 00:00:00') {
            $sendMail = true;
        } else {
            $diffHours = sprintf('%2d', (strtotime(date("Y-m-d H:i:s")) -
                strtotime($alert->created_at)) / 3600);
        
            if ($diffHours >= 12) {
                $sendMail = true;
            }
        }
    }
}
