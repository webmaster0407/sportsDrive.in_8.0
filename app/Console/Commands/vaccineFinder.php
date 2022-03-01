<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class vaccineFinder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vaccine:finder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email the available slots vaccine details';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /*$districtCode = "391";
        $date = date("d-m-Y");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://cdn-api.co-vin.in/api/v2/appointment/sessions/public/calendarByDistrict?district_id=$districtCode&date=$date",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "postman-token: c518908c-b89e-f9d2-47b4-04713b9b61a4",
                "token: WDckxXLvgDOn2XssQKD1hSgoi6ZehH7MBZgVLwhw30InaISltVmQPOrpYZrKv1PGzPl6iM1MNMG4DDaBs37aJGJAEEZeEVsZ4EOEi0 gxQ4="
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        }
        $response = json_decode($response,true);
        foreach($response['centers'] as $res){
            if($res['fee_type'] == "Free" && ( $res['pincode'] == "413705" ||  $res['pincode'] == "413706" ) ){
                foreach ($res['sessions'] as $session){
                    if($session['available_capacity'] > 0 && $session['available_capacity_dose1'] > 0 ){
                        $message = $session['vaccine']." is available at ".$res['name']." on Date:".$session['date']. ". No of 1st Dose :  ".$session['available_capacity_dose1']. ". No of 2nd Dose : ".$session['available_capacity_dose2']." For Age ".$session['min_age_limit']." +. Go to https://selfregistration.cowin.gov.in/";
                        Log::info($message);
                        $this->sendMessage("-1001232759597",$message,"1816183058:AAHJzv6hNhzO8dL4huPKlKD0tiP3LvMD4Gc");
                    }
                }
            }
        }*/

        $pins = array("413706","413705");
        foreach ($pins as $pin){
            $date = date("d-m-Y");
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://cdn-api.co-vin.in/api/v2/appointment/sessions/public/calendarByPin?pincode=$pin&date=$date",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "postman-token: c518908c-b89e-f9d2-47b4-04713b9b61a4",
                    "token: WDckxXLvgDOn2XssQKD1hSgoi6ZehH7MBZgVLwhw30InaISltVmQPOrpYZrKv1PGzPl6iM1MNMG4DDaBs37aJGJAEEZeEVsZ4EOEi0 gxQ4="
                ),
            ));
            Log::info($curl);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                echo "cURL Error #:" . $err;
            }
            $response = json_decode($response,true);
            Log::info($response);
            foreach($response['centers'] as $res){
                Log::info($res);
                if($res['fee_type'] == "Free" && ( $res['pincode'] == "413705" ||  $res['pincode'] == "413706" ) ){
                    foreach ($res['sessions'] as $session){
                        Log::info($session);
                        if($session['available_capacity'] > 0 && $session['available_capacity_dose1'] > 0  && $session['min_age_limit'] == 18){
                            $message = $session['vaccine']." is available at ".$res['name']." on Date:".$session['date']. ". No of 1st Dose :  ".$session['available_capacity_dose1']. ". No of 2nd Dose : ".$session['available_capacity_dose2']." For Age ".$session['min_age_limit']." +. Go to https://selfregistration.cowin.gov.in/";
                            Log::info($message);
                            $this->sendMessage("-1001232759597",$message,"1816183058:AAHJzv6hNhzO8dL4huPKlKD0tiP3LvMD4Gc");
                        }
                    }
                }
            }
            sleep(5);
        }
    }

    function sendMessage($chatID, $messaggio, $token) {
        $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chatID;
        $url = $url . "&text=" . urlencode($messaggio);
        $ch = curl_init();
        $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $optArray);
        $result = curl_exec($ch);
        Log::info($result);
        curl_close($ch);
        return $result;
    }
}
