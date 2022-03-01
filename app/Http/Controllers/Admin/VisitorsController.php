<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Customer;
use App\Http\Controllers\Controller;
use App\Order;
use App\Visitors;
use App\VisitorsPages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VisitorsController extends Controller {
	public function __construct() {
		$this->middleware('adminauth');
	}

	public function listVisitors(Request $request) {
		try {

            $request->session()->put('ipsearch', '');
            $request->session()->put('searchId', '');
            $request->session()->put('country', 'India');
            $customers = Customer::orderBy("created_at","DESC")->get();
            $countries = Visitors::groupBy("countryName")->pluck("countryName");
            if($request->has('type') && $request->type == "county"){
                $data =    Visitors::where("countryName","LIKE",$request->country)->with("customer","VisitorsPages","Notifications")->orderBy("updated_at","DESC")->paginate(50);
                $request->session()->put('country', $request->country);
            }elseif(!empty(session("country")) && !isset($request->searchByIp) && !isset($request->searchId)){
                $data =    Visitors::where("countryName","LIKE",session("country"))->with("customer","VisitorsPages","Notifications")->orderBy("updated_at","DESC")->paginate(50);
            }else{
                if(isset($request->searchByIp) && isset($request->searchId)){
                    $data =    Visitors::where("ip_address",$request->searchByIp)->where("customer_id",$request->searchId)->with("customer","VisitorsPages","Notifications")->orderBy("updated_at","DESC")->paginate(50);
                    $request->session()->put('ipsearch', $request->searchByIp);
                    $request->session()->put('searchId', $request->searchId);
                }elseif(isset($request->searchByIp)){
                    $data =    Visitors::where("ip_address",$request->searchByIp)->with("customer","VisitorsPages","Notifications")->orderBy("updated_at","DESC")->paginate(50);
                    $request->session()->put('ipsearch', $request->searchByIp);
                }elseif(isset($request->searchId)) {
                    $data =    Visitors::where("customer_id",$request->searchId)->with("customer","VisitorsPages","Notifications")->orderBy("updated_at","DESC")->paginate(50);
                    $request->session()->put('searchId', $request->searchId);
                }else{
                    $data =    Visitors::orderBy("updated_at","DESC")->with("customer","VisitorsPages","Notifications")->orderBy("updated_at","DESC")->paginate(50);
                }
            }
            $totalVisits = [];
            foreach ($data as $key=>$visitor){
                $totalVisits[$visitor->id] = Visitors::where("ip_address",$visitor->ip_address)->count();
            }
            return view('admin.list-visitors', compact('data','customers','totalVisits','countries'))->with('i', ($request->input('page', 1) - 1) * 50);
		} catch (\Exception $e) {
			$data = [
				'input_params' => $request,
				'action' => 'Admin list Customer Pages',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}

    public function visitorsDetails(Request $request,$id) {
        try {
            $visitors = Visitors::where("id",$id)->with("customer")->first();
            $visitorsPages = VisitorsPages::where("visitor_id",$id)->orderBy("created_at","DESC")->get();
            return view('admin.view-visitors-details', compact('visitors','visitorsPages'));
        } catch (\Exception $e) {
            $data = [
                'input_params' => $request,
                'action' => 'Admin list Customer Pages',
                'exception' => $e->getMessage(),
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
}
