<?php
namespace App\Http\Controllers\Admin;
use App\Address;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Validator;
use DB;
use App\Http\Requests;
use App\Admin;
use App\CmsPage;
use App\Banner;
use Carbon\Carbon;
use App\Subscriber;
use App\Newsletter;
use App\Customer;
use App\Order;

class CustomerController extends Controller
{
    public function __construct()
    {
       $this->middleware('adminauth');
    }

    public function listCustomer(Request $request) {
        try{
            $data = Customer::orderby("created_at", "DESC")->get();
            foreach ($data as $key => $customer) { 
                $orderCount[$customer->id] = Order::where('customer_id',$customer->id)->where('is_completed','Y')->count();  
                $addressCount[$customer->id] = Address::where('customer_id',$customer->id)->count();
            }
            return view('admin.list-customer')->with(compact('data','orderCount',"addressCount"));
        }catch(\Exception $e){
            $data = [
                'input_params' => $request,
                'action' => 'Admin list Customer Pages',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function addCustomer(){
    	try{
	        $data = array(
                "id"=>"",
	            "first_name" => "",
                "last_name" => "",
	            "email_address" => "",
                "phone"=>"",
	            "mode" => "add",
	            "password" => "",
	        );
	        $object1 = (object) $data;
        return view('admin.add-customer')->with('data', $object1);
        }catch(\Exception $e){
            $data = [
                'input_params' => NULL,
                'action' => 'Admin Add customer',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function editCustomer($id){
    	try{ 
	        $details = Customer::where('id',$id)->first();
	        if($details != null){
	        	 $details->mode = 'edit';
	        }
	        return view('admin.add-customer')->with('data', $details);
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
     public function addCustomerData(Request $request){
        $rules = array(
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email_address' => 'required|email|unique:customers,email_address',
            'password' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            $data = $request->all();
            $password = $data['password'];
            $siteDetails = Admin::select('facebook_url','twitter_url','googleplus_url','instagram_url')->first();
            $dataInsert = array(
                'first_name' =>  $data['first_name'],
                'last_name' =>  $data['last_name'],
                'email_address' =>  $data['email_address'],
                "phone" => $data['phone'],
                "password" => bcrypt($data['password']),
                'is_active'=>'Y', 
                "is_subscriber" => 'Y',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            );
            $insert = Customer::insertGetId($dataInsert);
            // registered email send 
            Mail::send('admin.emails.users-registration', ['first_name' => $data['first_name'],'siteDetails'=>$siteDetails,'email_address' => $data['email_address'],'password' => $password], function ($m) use ($data) {
                    $m->subject('Sports Drive | You are registered successfully');
                    $m->from(ENV('MAIL_FROM_EMAIL_ID'),'Sports Drive');
                    $m->to($data['email_address'])->subject('Sports Drive | You are registered successfully');
                    $m->cc(ENV('MAIL_FROM_EMAIL_ID'),'Sports Drive')->subject('Sports Drive | You are registered successfully');
                });

            if ($insert > 0 ) {
                return redirect('/administrator/list-customer')->with('success', 'customer added successfully.');
            } else {
                return redirect('/administrator/list-customer')->with('error', 'customer not added successfully.');
            }
        }
    }
     public function updateCustomerData(Request $request){
        $rules = array(
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email_address' => 'required|email',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
           return Redirect::back()->withInput()->withErrors($validator);
        } else {
            $data = $request->all();
            $id  = $data['customer_id'];
            $arryUpdate = array(
                 'first_name' =>  $data['first_name'],
                'last_name' =>  $data['last_name'],
                'email_address' =>  $data['email_address'],
                "phone" => $data['phone'],
                'updated_at' => Carbon::now(),
            );
            if($data['password'] != "notchanged!" && $data['password'] != NULL){
                $arryUpdate['password'] = bcrypt($data['password']);
            }
            $updateval = Customer::where('id', $id)->update($arryUpdate);
            if ($updateval == 1) {
                return redirect('/administrator/list-customer')->with('success', 'Customer updated successfully.');
            } else {
                return redirect('/administrator/list-customer')->with('error', 'Not updated successfully. Please try again.');
            }
        }
    }
    public function changeStatusCustomer(Request $request) {
        try{
            $data = $request->all();

            $operationFlag = $data['operationFlag'];
            $cID = $data['chk'];
            $updateVal = 0;
            $message = "Something went wrong!Please try again";
            if ($operationFlag == 'active') {
                $updateVal = Customer::whereIn('id', $cID)->update(array('is_active' => 'Y'));
                $message = "Customer/s successfully activated.";
            } else if ($operationFlag == 'deactive') {
                $updateVal = Customer::whereIn('id', $cID)->update(array('is_active' => 'N'));
                $message = "Customer/s successfully deactivated.";
            } else if ($operationFlag == 'delete') {
                $updateVal = Customer::whereIn('id', $cID)->delete();
                $message = "Customer/s successfully deleted.";
            }

            if ($updateVal > 0) {
                return redirect("/administrator/list-customer")->with('success', $message);
            } else {
                return redirect("/administrator/list-customer")->with('error', $message);
            }
        }catch(\Exception $e){ 
            $data = [
                'input_params' => $request->all(),
                'action' => 'change Status of Customer ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

   public static function getUniqueID() {
        $IDLEN = 6;
        $transOrdID = null;
        srand((double) microtime() * 1000000);

        $tmpordID = "";
        while (strlen($tmpordID) < $IDLEN) {
            $r = rand(1, 3);
            if ($r == 1) {
                $rcode = rand(48, 57);
            }
            if ($r == 2) {
                $rcode = rand(48, 57);
            }
            if ($r == 3) {
                $rcode = rand(48, 57);
            }
            $tmpordID.=chr($rcode);
        }
        $transOrdID =  $tmpordID ;

        return $transOrdID;
    }

    public function viewCustomerAddress($customer_id){
         try{
              $customer = Customer::where("id",$customer_id)->first();
              $shippingAddresses = Address::where("customer_id",$customer_id)->where("is_shipping","Y")->get();
              $billingAddresses = Address::where("customer_id",$customer_id)->where("is_billing","Y")->get();
             return view('admin.view-address')->with(compact("customer","shippingAddresses","billingAddresses"));
         }catch(\Exception $e){
             $data = [
                 'input_params' => $customer_id,
                 'action' => 'view Customer Address by admin',
                 'exception' => $e->getMessage()
             ];
             Log::info(json_encode($data));
             abort(500);
         }
    }

    public function editCustomerAddress($address_id){
        try{
            $addresses = Address::where("id",$address_id)->first();
            return view('admin.edit-customer-address')->with(compact("addresses"));
        }catch(\Exception $e){
            $data = [
                'input_params' => $address_id,
                'action' => 'edit Customer Address by admin',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function updateCustomerAddress(Request $request){
        try{
            $data = $request->except("_token");
            $addressId = $data['address_id'];
            unset($data['address_id']);
            $address = Address::where("id",$addressId)->first();
            $address->update($data);
           return redirect("/administrator/view-address/$address->customer_id")->with('success', 'customer address updated successfully.');
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'update Customer Address by admin',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function downloadCustomerData(){
        try{
            $customers = Customer::where("customers.is_active","Y")->with("address")->get();
            $fileName = "customerData.csv";
            $fp = fopen($fileName, 'w');
            $headers = array("First Name","Last Name","Email Address","Phone","City","State","Country","Pin Code");
            fputcsv($fp,$headers);
            foreach ($customers as $customer){
                $data['first_name'] = $customer['first_name'];
                $data['last_name'] = $customer['last_name'];
                $data['email_address'] = $customer['email_address'];
                $data['phone'] = $customer['phone'];
                if(isset($customer['address'])){
                    $data['city'] = $customer['address']['city'];
                    $data['state'] = $customer['address']['state'];
                    $data['country'] = $customer['address']['country'];
                    $data['pin_code'] = $customer['address']['pin_code'];
                }
                fputcsv($fp,$data);
            }
            return Response::download($fileName);
        }catch(\Exception $e){
            $data = [
                'input_params' => NULL,
                'action' => 'downloadCustomerData',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
}
