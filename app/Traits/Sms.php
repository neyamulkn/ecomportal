<?php
namespace App\Traits;


use App\Models\SiteSetting;
use Illuminate\Support\Facades\Auth;

trait Sms
{
    function sendSms($contact,$msg) {

        $url = "http://portal.metrotel.com.bd/smsapi";
        $data = [
            "api_key" => "R20001195fc633224289f9.65717764",
            "type" => "text",
            "contacts" => $contact,
            "senderid" => "8809612441161",
            "msg" => $msg,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}




