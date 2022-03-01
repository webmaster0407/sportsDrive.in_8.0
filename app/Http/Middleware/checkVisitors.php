<?php

namespace App\Http\Middleware;

use App\Customer;
use App\ExcludeIps;
use App\Http\Controllers\CustomTraits\VisitorsTrait;
use App\Visitors;
use App\VisitorsPages;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class checkVisitors
{
    use VisitorsTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $ip = $this->get_client_ip();
            /*If ip is of Altaf then neglect the visitor #start*/
            $status = $this->checkAltafIP($ip);
            /*If ip is of Altaf then neglect the visitor #end*/
            if (filter_var($ip, FILTER_VALIDATE_IP) && $status == 1){
                $user = Auth::user();
                $visitor_id = session('visitor_id');
                $visitorDetails = [];
                if($visitor_id != null){
                    $visitorDetails = Visitors::where("id",$visitor_id)->where("ip_address",$ip)->first();
                }
                $previousDetails = Visitors::where("ip_address",$ip)->where("customer_id","!=",null)->first();//check Ip is in the DB
                //$visitorDetails not exists OR check visits is belongs to today or not, if not then we need to add entry
                if($visitor_id == null || count($visitorDetails) == 0){//if visitor id not available then get the details
                    //GET AND STORE LOCATION DETAILS
                    $visitorId = $this->getLocationAndStore($ip);
                    $visitor = new VisitorsPages();
                    $visitor->url = $request->fullUrl();
                    $visitor->visitor_id = $visitorId;
                    $visitor->save();
                    if($visitorId != -1){
                        Visitors::where("id",$visitorId)->update(array("updated_at"=>Carbon::now()));
                    }
                    //check customers comes online as guest Or new user came online
                    $previousDetails = Visitors::where("ip_address",$ip)->where("customer_id","!=",null)->first();//check Ip is in the DB
                    if($previousDetails){
                        $customer = Customer::find($previousDetails->customer_id);
                        $customer->visitorId = $visitorId;
                    }else{
                        $customer = (object) [];
                        $customer->visitorId = $visitorId;
                        $customer->first_name = "Visitor";
                        $customer->last_name = "";
                        $customer->first_name = "Visitor";
                        $customer->id = "-1";
                    }
                    if(!$user && $previousDetails){//if guest but previous details exists
                        //fire a event notification
                        $customer = Customer::find($previousDetails->customer_id);
                        $this->StoreNotificationData($customer, "guest_online");
                    }else{
                        $this->StoreNotificationData($customer, "visitor_online");
                    }
                }else{// if details exists then add visit of users
                    if($visitorDetails->customer_id == null && $user != null){//if customer is logged in after comming to website update visitors data
                        $visitorDetails->customer_id = $user->id;
                        $visitorDetails->save();
                    }elseif ( $user != null && $visitorDetails->customer_id != $user->id){//if another user comes from same ip then add another entry
                        $visitorId = $this->getLocationAndStore($ip);
                        $visitor = new VisitorsPages();
                        $visitor->url = $request->fullUrl();
                        $visitor->visitor_id = $visitorId;
                        $visitor->save();
                        if($visitorId != -1){
                            Visitors::where("id",$visitorId)->update(array("updated_at"=>Carbon::now()));
                        }
                    }elseif ($user == null && count($previousDetails)>0){ /*check previously S/he came or not #start*/
                        Visitors::where("id",$visitorDetails->id)->update(array("customer_id"=>$previousDetails->customer_id));
                    }/*check previously S/he came or not #end*/
                    $visitor = new VisitorsPages();
                    $visitor->url = $request->fullUrl();
                    $visitor->visitor_id = $visitorDetails->id;
                    $visitor->save();
                    if($visitorDetails->id != -1){
                        Visitors::where("id",$visitorDetails->id)->update(array("updated_at"=>Carbon::now()));
                    }
                }
            }
        } catch (\Exception $e) {
            $data = [
                'input_params' => $request->all(),
                'action' => 'check visitors middleware',
                'exception' => $e->getMessage(),
            ];
            Log::info(json_encode($data));
        }
        return $next($request);
    }
    public function checkAltafIP($ip){
        $ip = ExcludeIps::where("ip",$ip)->get();
        if(count($ip)>0)
            return 0;
        else
            return 1;
    }
}
