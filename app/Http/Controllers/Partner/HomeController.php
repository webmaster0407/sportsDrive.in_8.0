<?php

namespace App\Http\Controllers\Partner;

use App\Admin;
use App\Http\Controllers\CustomTraits\LoginTraits;
use App\Http\Requests\adminlogin;
use App\Http\Requests\ChangePassword;
use App\Http\Requests\resetpassword;
use App\OtpVerification;
use App\Http\Controllers\Controller;
use App\Partner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;

class HomeController extends Controller
{
    use LoginTraits;
    public function __construct()
    {
        $this->middleware('partnerauth')->except("login","logout","forgotPassword","adminSendLink","adminredirectLink","adminresetPassword","loginCheck");
    }
    public function loginCheck()
    {
        try{
            $partner = session('partner');
            if($partner== null)
                return view('auth.partner-login');
            else
                return redirect("/partner/home");
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

    public function login(adminlogin $request)
    {
        try {
            $partner = Partner::where('email_address',$request->admin_email)->first();
            if($partner){
                $password = base64_decode($partner['password']);
                if($password == $request->password){
                    session(['partner' => $partner]);
                    return redirect("/partner/home");
                }
                else
                    return redirect()->back()->with("error","Invalid credential's");
            }
            else{
                return redirect()->back()->with("error","Invalid credential's");
            }
        }catch (\Exception $exception){
            $data = [
                'input_params' => $request->all(),
                'action' => 'Login partner data',
                'exception' => $exception->getMessage(),
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function logout(Request $request)
    {
        session()->forget('partner');
        return redirect("partner/login");
    }

    public function index(){
        try{
            return view('partner.home');
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


    public function changePassword(ChangePassword $request){

        try{
            $data = $request->all();
            $partner = session("partner");
            $user = Partner::where("id",$partner['id'])->first();
            if (($data['old_password'] == base64_decode($user->password))) {
                //get email address of customer
                if(!empty($user)){
                    $partner = session('partner');
                    Partner::where('id',$partner['id'])->update(array('password'=>base64_encode($data['password'])));
                    $message = 'Your password has been successfully updated.';
                    return redirect('partner/change-password')->with('success',  $message);
                }else{
                    $message = "Something went wrong| please try again";
                    // redirection path
                    return redirect('partner/change-password')->with('error',  $message);
                }
            } else {
                $message = "You have entered wrong old password | please try again";
                return redirect('partner/change-password')->with('error',  $message);
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
}
