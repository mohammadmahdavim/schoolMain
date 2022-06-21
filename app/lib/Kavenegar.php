<?php

namespace App\lib;

class Kavenegar
{

    public static function sendSMS($mobile,$token,$template){

        /*
         * Author: M.Fakhrani
         * Description : for send sms whit algorithm on kavenegar by API
         */
        $url = 'http://atrakweb.ir/api/v1/sms/' . env('API_KEY_SMS') . '/verify?receptor=' . $mobile . '&token=' . $token .'&template=' . $template;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec ($ch);
        curl_close ($ch);
        return $response;

    }

}

?>
