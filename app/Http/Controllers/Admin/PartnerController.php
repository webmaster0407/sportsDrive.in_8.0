<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\coupons;
use App\Customer;
use App\Partner;
use App\PartnerCoupons;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('adminauth');
    }

    public function listPartner(Request $request) {
        try{
            $data = Partner::orderby("created_at", "DESC")->get();
            $orderCount = array();
            $addressCount = array();
            return view('admin.partner.list-partner')->with(compact('data','orderCount',"addressCount"));
        }catch(\Exception $e){
            $data = [
                'input_params' => $request,
                'action' => 'Admin list partner',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function addPartner(){
        try{
            $coupons = coupons::all();
            $partnerCoupons = array();
            $data = array(
                "id"=>"",
                "first_name" => "",
                "last_name" => "",
                "email_address" => "",
                "password" => "",
                "phone"=>"",
                "pan_no"=>"",
                "facebook"=>"",
                "instagram"=>"",
                "linkedin"=>"",
                "address"=>"",
                "city"=>"",
                "flat_comission"=>"",
                "commission_type"=>"per",
                "mode" => "add",
                "coupons" =>$coupons,
            );
            $data = (object) $data;
            return view('admin.partner.add-partner')->with(compact("data","partnerCoupons"));
        }catch(\Exception $e){
            $data = [
                'input_params' => NULL,
                'action' => 'Admin Add partner',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function editPartner($id){
        try{
            $data = Partner::where('id',$id)->first();
            $data->coupons = coupons::all();
            if($data->flat_comission != null){
                $data->commission_type = "flat";
            }
            $data->password = "notchanged!";
            $partnerCoupons = PartnerCoupons::where("partner_id",$id)->pluck("coupon_id")->toArray();
            if($data != null){
                $data->mode = 'edit';
            }
            return view('admin.partner.add-partner')->with(compact("data","partnerCoupons"));
        }catch(\Exception $e){
            $data = [
                'input_params' => $id,
                'action' => 'Admin Edit customer',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function addPartnerData(Request $request){
        $rules = array(
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email_address' => 'required|email|unique:partners,email_address',
            'pan_no' => 'required|string|unique:partners,pan_no',
        );
        if($request->commission_type =="flat"){
            $rules['flat_comission'] = 'required|between:0,99.99';
        }
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            $data = $request->all();
            if(PartnerCoupons::whereIn("coupon_id",$data['coupons'])->count()>0){
                $message = "Coupons that you have selected for this partner is already selected for some other partner, Please try again";
                return Redirect::back()->withInput()->with("error",$message);
            }
            $dataInsert = array(
                'first_name' =>  $data['first_name'],
                'last_name' =>  $data['last_name'],
                'email_address' =>  $data['email_address'],
                "phone" => $data['phone'],
                'address' =>  $data['address'],
                'city' =>  $data['city'],
                'instagram' =>  $data['instagram'],
                "facebook" => $data['facebook'],
                'linkedin' =>  $data['linkedin'],
                'pan_no' =>  $data['pan_no'],
                'flat_comission' =>  $data['flat_comission'],
                "password" => base64_encode($data['password']),
                'is_active'=>'Y',
                "is_subscriber" => 'Y',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            );
            $insert = Partner::insertGetId($dataInsert);
            foreach ($data['coupons'] as $coupon){
                $couponData = [
                    "partner_id" => $insert,
                    "coupon_id" => $coupon,
                    "created_at" => Carbon::now(),
                ];
                /*Insert Coupons*/
                PartnerCoupons::insert($couponData);
            }
            if ($insert > 0 ) {
                return redirect('/administrator/list-partner')->with('success', 'Partner added successfully.');
            } else {
                return redirect('/administrator/list-partner')->with('error', 'Partner not added successfully.');
            }
        }
    }
    public function updatePartnerData(Request $request){
        $rules = array(
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email_address' => 'required|email',
        );
        if($request->commission_type == "flat"){
            $rules['flat_comission'] = 'required|between:0,99.99';
        }
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            $data = $request->all();
            $partner = Partner::where('id', $data['customer_id'])->first();
            if(PartnerCoupons::whereIn("coupon_id",$data['coupons'])->whereNotIn("partner_id",[$data['customer_id']])->count()>0){
                $message = "Coupons that you have selected for this partner is already selected for some other partner, Please try again";
                return Redirect::back()->withInput()->with("error",$message);
            }
            $id  = $data['customer_id'];
            if($data['password'] == "notchanged!"){
                $password = $partner->password;
            }else{
                $password = base64_encode($data['password']);
            }
            $arryUpdate = array(
                'first_name' =>  $data['first_name'],
                'last_name' =>  $data['last_name'],
                'email_address' =>  $data['email_address'],
                "password" => $password,
                "phone" => $data['phone'],
                'address' =>  $data['address'],
                'city' =>  $data['city'],
                'instagram' =>  $data['instagram'],
                "facebook" => $data['facebook'],
                'linkedin' =>  $data['linkedin'],
                'pan_no' =>  $data['pan_no'],
                'flat_comission' =>  $data['flat_comission'],
                'updated_at' => Carbon::now(),
            );
            $updateval = Partner::where('id', $id)->update($arryUpdate);
            /*update partner coupons*/
            $partnerCoupons = PartnerCoupons::where("partner_id",$id)->delete();
            foreach ($data['coupons'] as $coupon){
                if(PartnerCoupons::where("partner_id",$id)->where("coupon_id",$coupon)->count() == 0){
                    $couponData = [
                        "partner_id" => $id,
                        "coupon_id" => $coupon,
                        "created_at" => Carbon::now(),
                    ];
                    /*Insert Coupons*/
                    PartnerCoupons::insert($couponData);
                }

            }
            if ($updateval == 1) {
                return redirect('/administrator/list-partner')->with('success', 'Partner updated successfully.');
            } else {
                return redirect('/administrator/list-partner')->with('error', 'Not updated successfully. Please try again.');
            }
        }
    }
    public function changeStatusPartner(Request $request) {
        try{
            $data = $request->all();

            $operationFlag = $data['operationFlag'];
            $cID = $data['chk'];
            $updateVal = 0;
            $message = "Something went wrong!Please try again";
            if ($operationFlag == 'active') {
                $updateVal = Partner::whereIn('id', $cID)->update(array('is_active' => 'Y'));
                $message = "Partner/s successfully activated.";
            } else if ($operationFlag == 'deactive') {
                $updateVal = Partner::whereIn('id', $cID)->update(array('is_active' => 'N'));
                $message = "Partner/s successfully deactivated.";
            } else if ($operationFlag == 'delete') {
                $updateVal = Partner::whereIn('id', $cID)->delete();
                PartnerCoupons::where("partner_id",$cID)->delete();
                $message = "Partner/s successfully deleted.";
            }

            if ($updateVal > 0) {
                return redirect("/administrator/list-partner")->with('success', $message);
            } else {
                return redirect("/administrator/list-partner")->with('error', $message);
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'change Status of Partner ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public static function getUniqueID($n) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }

}
