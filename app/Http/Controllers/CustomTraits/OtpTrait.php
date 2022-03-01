<?php
namespace App\Http\Controllers\CustomTraits;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait OtpTrait{
    public function createOtp($length = 6){
        try{
            $alphabet = '1234567890';
            $pass = array(); //remember to declare $pass as an array
            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
            for ($i = 0; $i < $length; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            return implode($pass); //turn the array into a string
        }catch (\Exception $e){
            $errorLog = [
                'action'=>'create OTP',
                'exception'=>$e->getMessage()
            ];
            Log::critical(json_encode($errorLog));
            return null;
        }
    }

    public function sendSms($mobile,$otp,$userData){
        try{
            $otp = str_split($otp,3);
            $smsOTP = $otp[0];
            $emailOTP = $otp[1];
            $apiKey = urlencode('Du0mJi0yXJM-dcQZop4KrMSE0SZP0PTTDfGmCJkxSc');
            // Message details
            $sender = urlencode('SDrive');
            $message = rawurlencode("Thanks for showing interest in SportsDrive. Your first 3 digit for one-time password is $smsOTP. Your next 3 digits have been sent to your email address.");
            // Prepare data for POST request
            $data = array('apikey' => $apiKey, 'numbers' => $mobile, "sender" => $sender, "message" => $message);
            // Send the POST request with cURL
            $ch = curl_init('https://api.textlocal.in/send/');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);
            curl_close($ch);
            /*send sms code ends*/
            /*send a email for OTP #STARTS*/
            Mail::send('user.emails.send-otp', ['userData' => $userData,"otp"=>$emailOTP], function ($m) use ($userData,$emailOTP) {
                $m->subject('SportsDrive.In | Your last 3 digits of OTP for registration');
                $m->from(ENV('ADMIN_ORDER_EMAIL_ID'), 'SportsDrive.in');
                $m->to($userData['email_address'])->subject('SportsDrive.in | Your last 3 digits of OTP for registration');
                $m->cc(ENV('ADMIN_ORDER_EMAIL_ID'), 'SportsDrive.in')->subject('SportsDrive.in | Your last 3 digits of OTP for registration');
            });
            /*send a email for OTP #ENDS*/
            return $output;
        }catch (\Exception $e){
            $errorLog = [
                'mobile'=>$mobile,
                'otp'=>$otp,
                'action'=>'Send SMS OTP',
                'exception'=>$e->getMessage()
            ];
            Log::critical(json_encode($errorLog));
            return null;
        }
    }
}