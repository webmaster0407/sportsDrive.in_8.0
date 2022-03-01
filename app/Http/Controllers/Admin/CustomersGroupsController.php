<?php

namespace App\Http\Controllers\Admin;


use App\Admin;
use App\Customer;
use App\CustomersGroups;
use App\CustomersGroupsUsers;
use App\Jobs\SendEmail;
use App\User;
use App\UserGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Hash;

class CustomersGroupsController extends Controller
{
    public function __construct()
    {
        $this->middleware('adminauth');
    }

    /**
     * Display a listing of the customers groups.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $customersGroups = CustomersGroups::get();
            return view('admin.customer-groups.list-customer-groups')->with(compact('customersGroups'));
        }catch(\Exception $e){
            $data = [
                'input_params' => NULL,
                'action' => 'Admin list Customer groups',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try{
            $data = (object) array();
            $customers = Customer::get();
            $data->mode = "add";
            $data->id = "";
            $data->name = "";
            $selectedCustomers = array();
            return view('admin.customer-groups.add-customer-groups')->with(compact('customers',"data","selectedCustomers"));
        }catch (\Exception $e){
            $data = [
                'input_params' => NULL,
                'action' => 'Admin create Customer groups view',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $customerGroupsId = CustomersGroups::insertGetId(array("name"=>$request->name));
            foreach ($request->customers as $customer)
                CustomersGroupsUsers::insert(array("customer_group_id"=>$customerGroupsId,"user_id"=>$customer));
            return redirect('/administrator/list-customer-groups')->with('success', 'customer group added successfully.');
        }catch (\Exception $e){
            $data = [
                'input_params' => NULL,
                'action' => 'Admin create Customer groups view',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }



    /**
     * Show the form for editing the specified customers groups.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $data = CustomersGroups::where("id",$id)->with("customersGroupsUsers")->first();
            $customers = Customer::get();
            $selectedCustomers = array();
            if (count($data->customersGroupsUsers)>0)
                foreach ($data->customersGroupsUsers as $customers_groups_user)
                    $selectedCustomers[$customers_groups_user->user_id] = $customers_groups_user;
            $data->mode = "edit";
            return view('admin.customer-groups.add-customer-groups')->with(compact('customers',"data",'selectedCustomers'));
        }catch (\Exception $e){
            $data = [
                'input_params' => NULL,
                'action' => 'Admin create Customer groups view',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try{
            CustomersGroups::where("id",$request->customer_group_id)->update(array("name"=>$request->name));
            CustomersGroupsUsers::where("customer_group_id",$request->customer_group_id)->delete();
            foreach ($request->customers as $customer)
                CustomersGroupsUsers::insert(array("customer_group_id"=>$request->customer_group_id,"user_id"=>$customer));
            return redirect('/administrator/list-customer-groups')->with('success', 'customer group updated successfully.');
        }catch (\Exception $e){
            $data = [
                'input_params' => NULL,
                'action' => 'Admin create Customer groups view',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    /**
     * Delete the customers group
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        try{
            CustomersGroups::whereIn('id', $request->chk)->delete();
            CustomersGroupsUsers::whereIn('customer_group_id', $request->chk)->delete();
            return redirect('/administrator/list-customer-groups')->with('success', 'customer group deleted successfully.');
        }catch (\Exception $e){
            $data = [
                'input_params' => NULL,
                'action' => 'destroy customers groups',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public static function getUniqueID() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    public function uploadCSV(Request $request){
        try{
            $users = Excel::toArray("",$request->file('csv'), '', \Maatwebsite\Excel\Excel::XLSX);
            $data = array();
            $date = Carbon::now();
           /* unset($users[0][0]);*/
            $allEmails = array_map('trim', User::pluck("email_address")->toArray());
            $siteDetails = Admin::select('facebook_url','twitter_url','googleplus_url','instagram_url')->first();
            $allPasswords = DB::table("customer_passwords")->get()->toArray();
            $successCount = 0;
            $errorCount = 0;
            $noEmail = 0;
            if (count($users[0])>1){
               /* unset($users[0][0]);*/
                $temp = $users[0];
                $customerGroupsId = 0;
                $allEmails = array_flip($allEmails);
                $totalRecs = count($temp);
                foreach ($temp as $key => $user){
                    if($key >= $totalRecs-1){
                        /*send email to altaf and amol that uploading is about to complte*/
                        Mail::send('admin.emails.csv-upload-complition-email',[], function ($m) use ($data) {
                            $m->subject('SportsDrive.In | CSV is about to complte');
                            $m->from(ENV('MAIL_FROM_EMAIL_ID'),'SportsDrive.In');
                            $m->to("amolrokade121@gmail.com");
                            $m->cc("altafv@sportiff.in");
                        });
                    }
                    /*send email to altaf and amol that uploading is about to complte*/
                    if(trim($user[1]) != null){
                        $email = preg_replace('/[ \t]+/', ' ', preg_replace('/[\r\n]+/', "\n", $user[1]));
                        if(filter_var($email, FILTER_VALIDATE_EMAIL) && !(array_key_exists(trim($user[1]),$allEmails))){
                            if ($successCount == 0){
                                $customerGroupsId = CustomersGroups::insertGetId(array("name"=>$request->name));
                            }
                            $successCount++;
                            $password = $allPasswords[rand(0,1770)];
                            $name = explode(" ",$user[0]);
                            $nameCount  = count($name);
                            $mobile = explode("+91",$user[2]);
                            $mobile = str_replace(" ","",$mobile);
                            $data[$key]['customer_type'] = "individual";
                            if($nameCount > 1){
                                $data[$key]['first_name'] = $name[0];
                                $data[$key]['last_name'] = $name[$nameCount-1];
                            }else{
                                $data[$key]['first_name'] = $name[0];
                                $data[$key]['last_name'] = "";
                            }
                            $data[$key]['company_name'] = "";
                            $data[$key]['email_address'] = $email;
                            $data[$key]['phone'] = $mobile[1];
                            $data[$key]['alt_phone'] = "";
                            $data[$key]['password'] = $password->HashedPassword;
                            $data[$key]['is_active'] = "Y";
                            $data[$key]['remember_token'] = csrf_token();
                            $data[$key]['is_subscriber'] = "Y";
                            $data[$key]['created_at'] = $date;
                            $data[$key]['updated_at'] = $date;
                            $data[$key]['original_password'] = $password->password;
                            /*send an email #start*/
                            $details['data'] = $data[$key];
                            $details['siteDetails'] = $siteDetails;
                            $details['customerGroupsId'] = $customerGroupsId;
                            /*dispatch job to add user and send an email*/
                            dispatch(new SendEmail($details,$user));
                        }else{
                            $errorCount++;
                        }
                    }else{
                        $noEmail++;
                    }
                }
            }
            $msgData = [
                "successCount"=>$successCount,
                "errorCount"=>$errorCount,
                "noEmail"=>$noEmail,
            ];
            session("msgData",$msgData);
            return redirect('/administrator/list-customer')->with("msgData",$msgData);
        }catch (\Exception $e){
            $data = [
                'input_params' => NULL,
                'action' => 'upload CSV customers',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
}
