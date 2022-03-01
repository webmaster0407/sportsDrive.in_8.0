<?php
namespace App\Http\Controllers\CustomTraits;


use App\Notifications;
use App\Visitors;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait VisitorsTrait{
    public $user;
    public $customer;
    public $message;
    public $date;
    public $type;
    public $username;
    public $visitDetails;
    public $visitCustName;

    public function getLocationAndStore($ip)
    {
        try {
            $dateTime = Carbon::now();
            $userId = null;
            $user = Auth::user();
            if($user)
                $userId = $user->id;
            $ipdata = $this->getIpDetailsFromExtreemeIpLookUp($ip);
            if($ipdata != 404){
                $ipdata = json_decode($ipdata);
                if ($ipdata->city == null || $ipdata->city == ""){
                    $ipdata = json_decode($this->getIpDetailsFromIpAPI($ip));
                    if($ipdata->status != "fail"){
                        $data = array(
                            "customer_id" => $userId,
                            "ip_address" => $ip,
                            "city" => $ipdata->city,
                            "region" => $ipdata->region,
                            "regionCode" => $ipdata->region,
                            "regionName" => $ipdata->regionName,
                            "countryCode" => $ipdata->countryCode,
                            "countryName" => $ipdata->country,
                            "latitude" => $ipdata->lat,
                            "longitude" => $ipdata->lon,
                            "timezone" => $ipdata->timezone,
                            "isp_name" => $ipdata->isp,
                            "org" => $ipdata->org,
                            "details_by" => "www.ip-api.com",
                            "created_at" =>$dateTime,
                            "updated_at" =>$dateTime,
                        );
                        $visitor_id = session('visitor_id');
                        if(!$visitor_id){//if visitor id is not in session
                            $now = Carbon::now();
                            $checkPrevious = Visitors::where("ip_address",$ip)->first();
                            if (count($checkPrevious)>0 && strtotime($now) - strtotime($checkPrevious->created_at)>600) {//if previous exists and time is greater than 10 mins
                                $visitorId = Visitors::insertGetId($data);
                                session(['visitor_id' => $visitorId]);
                                return $visitorId;
                            }elseif(count($checkPrevious)>0 && strtotime($now) - strtotime($checkPrevious->created_at)<600){//if previous exists and time is less than 10 mins
                                session(['visitor_id' => $checkPrevious->visitor_id]);
                                return $checkPrevious->visitor_id;
                            }else{//if previous does not exists
                                $visitorId = Visitors::insertGetId($data);
                                session(['visitor_id' => $visitorId]);
                                return $visitorId;
                            }
                        }else{
                            return $visitor_id;
                        }
                    }else{
                        return -1;
                    }
                }else{
                    if ($ipdata->status != "fail"){
                        $data = array(
                            "customer_id" => $userId,
                            "ip_address" => $ip,
                            "city" => $ipdata->city,
                            "region" => $ipdata->region,
                            "regionCode" => $ipdata->region,
                            "regionName" => $ipdata->region,
                            "countryCode" => $ipdata->countryCode,
                            "countryName" => $ipdata->country,
                            "continentCode" => $ipdata->continent,
                            "continentName" => $ipdata->continent,
                            "latitude" => $ipdata->lat,
                            "longitude" => $ipdata->lon,
                            "isp_name" => $ipdata->isp,
                            "org" => $ipdata->org,
                            "details_by" => "www.extreme-ip-lookup.com",
                            "created_at" =>$dateTime,
                            "updated_at" =>$dateTime,
                        );
                        $visitor_id = session('visitor_id');
                        if(!$visitor_id){//if visitor id is not in session
                            $now = Carbon::now();
                            $checkPrevious = Visitors::where("ip_address",$ip)->first();
                            if (count($checkPrevious)>0 && strtotime($now) - strtotime($checkPrevious->created_at)>600) {//if previous exists and time is greater than 10 mins
                                $visitorId = Visitors::insertGetId($data);
                                session(['visitor_id' => $visitorId]);
                                return $visitorId;
                            }elseif(count($checkPrevious)>0 && strtotime($now) - strtotime($checkPrevious->created_at)<600){//if previous exists and time is less than 10 mins
                                session(['visitor_id' => $checkPrevious->visitor_id]);
                                return $checkPrevious->visitor_id;
                            }else{//if previous does not exists
                                $visitorId = Visitors::insertGetId($data);
                                session(['visitor_id' => $visitorId]);
                                return $visitorId;
                            }
                        }else{
                            return $visitor_id;
                        }
                    }else{
                        return -1;
                    }
                }
            }else{
                $ipdata = json_decode($this->getIpDetailsFromIpAPI($ip));
                if($ipdata->status != "fail"){
                    $data = array(
                        "customer_id" => $userId,
                        "ip_address" => $ip,
                        "city" => $ipdata->city,
                        "region" => $ipdata->region,
                        "regionCode" => $ipdata->region,
                        "regionName" => $ipdata->regionName,
                        "countryCode" => $ipdata->countryCode,
                        "countryName" => $ipdata->country,
                        "latitude" => $ipdata->lat,
                        "longitude" => $ipdata->lon,
                        "timezone" => $ipdata->timezone,
                        "isp_name" => $ipdata->isp,
                        "org" => $ipdata->org,
                        "details_by" => "www.ip-api.com",
                        "created_at" =>$dateTime,
                        "updated_at" =>$dateTime,
                    );
                    $visitor_id = session('visitor_id');
                    if(!$visitor_id){//if visitor id is not in session
                        $now = Carbon::now();
                        $checkPrevious = Visitors::where("ip_address",$ip)->first();
                        if (count($checkPrevious)>0 && strtotime($now) - strtotime($checkPrevious->created_at)>600) {//if previous exists and time is greater than 10 mins
                            $visitorId = Visitors::insertGetId($data);
                            session(['visitor_id' => $visitorId]);
                            return $visitorId;
                        }elseif(count($checkPrevious)>0 && strtotime($now) - strtotime($checkPrevious->created_at)<600){//if previous exists and time is less than 10 mins
                            session(['visitor_id' => $checkPrevious->visitor_id]);
                            return $checkPrevious->visitor_id;
                        }else{//if previous does not exists
                            $visitorId = Visitors::insertGetId($data);
                            session(['visitor_id' => $visitorId]);
                            return $visitorId;
                        }
                    }else{
                        return $visitor_id;
                    }
                }else{
                    return -1;
                }
            }
        } catch (\Exception $e) {
        $data = [
            'input_params' => $ip,
            'action' => 'check visitors middleware',
            'exception' => $e->getMessage(),
        ];
        Log::info(json_encode($data));
        }
    }

    function get_client_ip() {
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    function getIpDetailsFromGeoPlugin($ip){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://www.geoplugin.net/php.gp?ip=$ip",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Postman-Token: 719f2fda-c90b-424b-957a-22bd39be10ae"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return 404;
        } else {
            return $response;
        }
    }

    function getIpDetailsFromExtreemeIpLookUp($ip){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://extreme-ip-lookup.com/json/$ip",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Postman-Token: 719f2fda-c90b-424b-957a-22bd39be10ae"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return 404;
        } else {
            return $response;
        }
    }

    public function getIpDetailsFromIpAPI($ip)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://ip-api.com/json/$ip",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Postman-Token: 719f2fda-c90b-424b-957a-22bd39be10ae"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return 404;
        } else {
            return $response;
        }
    }

    public function StoreNotificationData($user, $type){
        try{
            $visitor_id = session('visitor_id');
            $previousDetails = Visitors::find($visitor_id);
            $city = "";
            if(count($previousDetails)>0)
                $city = $previousDetails->city;
            $date = Carbon::now();
            $this->date = $date->diffForHumans();
            if($user){
                $this->username = $user->first_name . " " . $user->last_name;
                $this->customer = $user;
            }else{
                $user = (object) [];
                $user->id = "-1";
                $user->username = "Visitor";
                $user->first_name = "Visitor";
                $user->last_name = "";
                $this->customer = $user;
            }
            $user->visitorId = $visitor_id;
            $this->type = $type;
            $this->visitDetails = null;
            $this->visitCustName = "";
            if($type == "login"){
                if(!$city)
                    $this->message = "{$this->username} is online.";
                else
                    $this->message = "{$this->username} from $city is online";
            }else if ($type == "register"){
                if($city)
                    $this->message = "{$this->username} from $city registered successfully.";
                else
                    $this->message = "{$this->username} is registered successfully.";
            }else if ($type == "logout"){
                if($city)
                    $this->message = "{$this->username} from $city is  logged out.";
                else
                    $this->message = "{$this->username} is  logged out.";

            }else if($type == "guest_online"){
                if($city)
                    $this->message = "{$this->username} from $city is come online as GUEST.";
                else
                    $this->message = "{$this->username} is come online as GUEST.";
            }else if($type == "visitor_online"){
                $visitor = Visitors::where("id",$user->visitorId)->with("customer")->first();
                $this->visitDetails  = $visitor;
                if($visitor->customer){
                    $this->visitCustName = $visitor->customer->first_name." ".$visitor->customer->last_name;
                }else{
                    $this->visitCustName = $this->username;
                }
                if($city)
                    $this->message = "{$this->username} from $city is come online.";
                else
                    $this->message = "{$this->username} is come online.";
            }else if($type == "add_cart"){
                if($city)
                    $this->message = "{$this->username} from $city is added {$user->productName} product to the cart.";
                else
                    $this->message = "{$this->username} is added {$user->productName} product to the shopping cart.";
            }else if($type == "update_cart"){
                if($city)
                    $this->message = "{$this->username} from $city is updated cart with product {$user->productName}.";
                else
                    $this->message = "{$this->username} is updated cart with product {$user->productName}.";
            }else if($type == "add_cart_not_available"){
                $this->message = "{$this->username} is trying to add product {$user->productName} to the shopping cart, but its not available.";
            }else if($type == "proceed_payment"){
                if($city)
                    $this->message = "{$this->username} from $city  has proceeded to Pay. Total Units in Cart are $user->cartCount and Total Cart Amount is Rs.$user->subTotal";
                else
                    $this->message = "{$this->username} has proceeded to Pay. Total Units in Cart are $user->cartCount and Total Cart Amount is Rs.$user->subTotal";
            }elseif($type == "checkout"){
                if($city)
                    $this->message = "{$this->username} from $city  has proceeded to CHECKOUT. Total Units in Cart are $user->cartCount and Total Cart Amount is Rs.$user->subTotal";
                else
                    $this->message = "{$this->username} has proceeded to CHECKOUT. Total Units in Cart are $user->cartCount and Total Cart Amount is Rs.$user->subTotal";
            }elseif($type == "search"){
                if($city)
                    $this->message = "{$this->username} from $city  is searching for \"$user->searckKeyword\"";
                else
                    $this->message = "{$this->username} is searching for  \"$user->searckKeyword\"";
            }
            $not_data = new Notifications();
            $not_data->user_id = $user->id;
            $not_data->notification = $this->message;
            $not_data->visitor_id = $user->visitorId;
            $not_data->save();
        }catch (\Exception $e){
            $data = [
                'input_params' =>$user,
                'action' => 'StoreNotificationData',
                'exception' => $e->getMessage(),
            ];
            Log::info(json_encode($data));
        }
    }
}
