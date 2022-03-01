<?php

namespace App\Http\Controllers\Admin;

use App\Address;
use App\OtpVerification;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use DB;
use App\Http\Requests;
use App\Admin;
class HomeController extends Controller
{
	public function __construct()
    {
        $this->middleware('adminauth')->except("login","logout","forgotPassword","adminSendLink","adminredirectLink","adminresetPassword","loginCheck");
    }
    public function loginCheck()
    {
         try{
           $admin = session('admin');
            if($admin== null)
              return view('auth.login');
            else  
              return redirect("/administrator/home");
          }catch(\Exception $e){
              $data = [
                  'input_params' => NULL,
                  'action' => 'login admin',
                  'exception' => $e->getMessage()
              ];
              Log::info(json_encode($data));
              abort(500);
          }
    }

    public function login(Requests\adminlogin $request)
    {
    	 $admin = Admin::where('admin_email',$request->admin_email)->first();
    	 if($admin){
             $password = base64_decode($admin['password']);
    	 	 if($password == $request->password){
                    session(['admin' => $admin]);
                    return redirect("/administrator/home");
                }
                else
                    return redirect()->back()->with("error","Invalid credential's");

    	 }
    	 else{
                return redirect()->back()->with("error","Invalid credential's");
            }
    }
    public function logout(Request $request)
    {
        $request->session()->forget('admin');
        return redirect("administrator/login");
    }
    public function forgotPassword(Request $request)
    {
    	return view("auth.forgot-password");
    }
    public function adminSendLink(Request $request)
    {
    	$data = $request->all();
    	$adminuser = Admin::where('admin_email',$data['admin_email'])->first();
    	if(!empty($adminuser)){
    		if($adminuser['remember_token']==null){
                    $adminuser['remember_token'] = csrf_token();
                    Admin::where('admin_email',$data['admin_email'])->update(array('remember_token'=>$adminuser['remember_token'] ));
            }
            $siteDetails = Admin::select('facebook_url','twitter_url','googleplus_url','instagram_url')->first();
            Mail::send('admin.emails.forgot-password', ['siteDetails' => $siteDetails,'admin_name' => $adminuser->admin_name, 'remember_token' => $adminuser->remember_token,'admin_email' => $adminuser->admin_email], function ($m) use ($adminuser) {
                        $m->subject('Sports Drive |Forgot password Link');
                        $m->from(ENV('INFO_EMAIL_ID'));
                        $m->to($adminuser->admin_email,'Sports Drive')->subject(' Sports Drive | Forgot password Link');
                });
                if(count(Mail::failures()) == 0){
                    return redirect()->back()->with('success',"Please check your mailbox to reset password.");
                }
    	}
    	else
            return redirect()->back()->with('error',"Something went wrong ! Please try again.");

    }
    public function adminredirectLink(Request $request,$remember_token){
            $user = Admin::where('remember_token',$remember_token)->first();
            if(!empty($user)){
                 return view("auth.passwords.reset")->with(compact('remember_token'));
            }
            return view("auth.forgot-password")->with('error',"Something went wrong ! Please try again.");
    }
    public function adminresetPassword(Requests\resetpassword $request,$remember_token){
    	    $data = $request->all();
            if($data['password'] != $data['confirmPassword']){
                return redirect()->back()->with('error-message',"Not matched confirm Password field ! Please try again.");
            }
            $user = Admin::where('remember_token',$remember_token)->first();
            if(!empty($user)){
            	 Admin::where('remember_token',$remember_token)->update(array('password'=>base64_encode($request->password),'remember_token'=>csrf_token()));
                session(['admin' => null]);
                return redirect('administrator/login')->with('success',"Password Reset Successfully, Please login to continue.");
            }
            return redirect('/admin-reset-password/'.$remember_token)->with('error',"Something went wrong ! Please try again.");
    }
    public function index(){
      try{
          return view('admin.home');
        }catch(\Exception $e){
            $data = [
                'input_params' => null,
                'action' => 'home',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function siteSettings(Request $request){
	    try{
            $admin = session('admin'); //dd($admin);
            $data = Admin::where('id',$admin['id'])->first();
            return view('admin.site-settings')->with(compact('data'));
        }catch(\Exception $e){
            $data = [
                'input_params' => $request,
                'action' => 'siteSettings',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
      
    }
    public function updateSiteSettings(Requests\SiteSettingRequest $request)
    {
        try{
    	    $data = $request->all(); //dd($data);
    	    $admin = session('admin');
            $arrdata = [
                'admin_email'=>$data['admin_email'],
                'site_heading'=>$data['site_heading'],
                "telephone" => $data['telephone'],
                "address" =>$data['address'],
                "facebook_url" =>$data['facebook_url'],
                "twitter_url" => $data['twitter_url'],
                "googleplus_url" => $data['googleplus_url'],
                "instagram_url" =>$data['instagram_url'],
                "youtube_url" =>$data['youtube_url'],
                "contact_telephone" =>$data['contact_telephone'],
                "contact_address" =>$data['contact_address'],

            ];
             $present  = Admin::where('id',$admin['id'])->first();

            if($present == null)
                $result = Admin::insert($arrdata);
            else
                $result = Admin::where('id',$admin['id'])->update($arrdata);
            //dd($result);
            if($result!=null || !empty($result)){
                return redirect('administrator/site-settings')->with("success","Site Settings Info Updated successfully.");
            }else
                return redirect('administrator/site-settings')->with("error","Site Settings Info not Updated successfully.");

        }catch(\Exception $e){
            $data = [
                'input_params' => $data,
                'action' => 'siteSettings',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function changePassword(Requests\ChangePassword $request){

        try{
            $data = $request->all();
    	    $user = Admin::first();
    	    if (($data['old_password'] == base64_decode($user->password))) {
                 //dd($user->password);
    	        //get email address of customer
    	        if(!empty($user)){
                     $admin = session('admin');
                    $siteDetails = Admin::select('facebook_url','twitter_url','googleplus_url','instagram_url')->where('id',$admin['id'])->first();
    	            Mail::send('admin.emails.change-password', ['siteDetails' => $siteDetails,'admin_name' => $user->admin_name, 'remember_token' => $user->remember_token,'admin_email' => $user->admin_email], function ($m) use ($user) {
    	                $m->subject('Sports Drive |Password is changed');
    	                $m->from(ENV('INFO_EMAIL_ID'),'Sports Drive');
    	                $m->to($user->admin_email)->subject(' Sports Drive | Password is changed');
    	                $m->cc(ENV('INFO_EMAIL_ID'),'Sports Drive')->subject('Sports Drive | Password is changed');
    	            });
    	            if(count(Mail::failures()) == 0){//if mail sent
    	                Admin::where('id',$admin['id'])->update(array('password'=>base64_encode($data['password'])));
    	                $message = 'Your password has been successfully updated.';
    	                return redirect('administrator/change-password')->with('success',  $message);
    	            }else{//problem in mail sending
    	                $message = "Something went wrong| please try again";
    	                // redirection path
    	                return redirect('administrator/change-password')->with('error',  $message);
    	            }
    	        }else{
    	            $message = "Something went wrong| please try again";
    	            // redirection path
    	            return redirect('administrator/change-password')->with('error',  $message);
    	        }
    	    } else {
    	        $message = "You have entered wrong old password | please try again";
    	        return redirect('administrator/change-password')->with('error',  $message);
    	    }
        }catch(\Exception $e){
                $data = [
                    'input_params' => $data,
                    'action' => 'changePassword',
                    'exception' => $e->getMessage()
                ];
                Log::info(json_encode($data));
                abort(500);
        }

    }

    public function listOTPs(){
        try{
            $otps = OtpVerification::orderBy("created_at","desc")->get();
            return view('admin.list-otps')->with(compact('otps'));
        }catch(\Exception $e){
            $data = [
                'input_params' => NULL,
                'action' => 'siteSettings',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function removeSpecialFromAddress(){
        $allAddresses = Address::all()->toArray();
        foreach ($allAddresses as $key=>$address){
            $address['address_line_1'] = trim(preg_replace('/[^A-Za-z0-9\-]/', ' ', $address['address_line_1']));
            $address['address_line_2'] = trim(preg_replace('/[^A-Za-z0-9\-]/', ' ', $address['address_line_2']));
            $address['city'] = trim(preg_replace('/[^A-Za-z0-9\-]/', ' ', $address['city']));
            $address['state'] = trim(preg_replace('/[^A-Za-z0-9\-]/', ' ', $address['state']));
            $address['country'] = trim(preg_replace('/[^A-Za-z0-9\-]/', ' ', $address['country']));
            Address::where("id",$address['id'])->update($address);
        }
        dd("done");
    }

}
