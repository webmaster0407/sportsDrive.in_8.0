<?php

namespace App\Http\Controllers;

use App\Address;
use App\Admin;
use App\CmsPage;
use App\Customer;
use App\Events\StatusLiked;
use App\ExcludeIps;
use App\Http\Controllers\CustomTraits\LoginTraits;
use App\Http\Controllers\CustomTraits\OtpTrait;
use App\Http\Controllers\CustomTraits\VisitorsTrait;
use App\Http\Requests;
use App\OtpVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller {
	use LoginTraits;
	use VisitorsTrait;
    use OtpTrait;
	/**
	 * UserController constructor.
	 */
	public function __construct() {
		$this->middleware('userauth')->only("AddressList", "changePassword", "address", "editAddress");
        $this->middleware('checkVisitors')->except("autoLogin");
	}

	public function logout(Request $request) {
		try {
		    $user = Auth::user();
            session()->forget("order_id");
            Auth::logout();
            $this->StoreNotificationData($user, "logout");
			return redirect("/login");
		} catch (\Exception $e) {
			$data = [
				'input_params' => Auth::user(),
				'action' => 'logout User',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}
	public function login() {
		try {
			$user = Auth::user();
			if ($user == null) {
				return view('user.login');
			} else {
				return redirect("/");
			}

		} catch (\Exception $e) {
			$data = [
				'input_params' => NULL,
				'action' => 'Register User',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}

	public function register() {
		try {
			return view('user.register');
		} catch (\Exception $e) {
			$data = [
				'input_params' => NULL,
				'action' => 'Register User view',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}

	public function loginData(Requests\loginRequest $request) {
		try {
			$data = $request->all();
			$user = Auth::user();
			if ($user) {
                return redirect("/");
			}
			$response = $this->loginUser($data);
			if ($response == "200") {
				$user = Auth::user();
                $this->StoreNotificationData($user, "login");
				$url = session()->get("url");
				if (!$url) {
					$url = "/";
				}
				return redirect($url)->with('success', 'Successfully Logged In');
			} elseif ($response == "403") {
				return redirect()->back()->with("error", "Invalid credential's");
			} else {
				return redirect()->back()->with("error", "Something went wrong! Please try again.");
			}
		} catch (\Exception $e) {
			$data = [
				'input_params' => $request->all(),
				'action' => 'Login user data',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}


	public function registerUser(Requests\registerRequest $request) {
		try {
		   /* $validateOTP = $this->validateOtp($request->phone,$request->otp);
		    if($validateOTP['status'] != 200){
                return response()->json(["data"=>null,"msg"=>"Sorry! We could not able to verify the otp","status"=>500],200);
            }*/
			$data = $request->all();
			if (isset($data['is_subscriber'])) {
				$is_subscriber = "Y";
			} else {
				$is_subscriber = "N";
			}
			$arrData = [
				"first_name" => $data['first_name'],
				"last_name" => $data['last_name'],
				"email_address" => $data['email_address'],
				"phone" => $data['phone'],
				"password" => bcrypt($data['password']),
				"is_subscriber" => $is_subscriber,
				"remember_token" => csrf_token(),
				"created_at" => Carbon::now(),
				"updated_at" => Carbon::now(),
			];
			$user = Customer::where('email_address', $data['email_address'])->first();
			if ($user == null) {
				$result = Customer::insertGetId($arrData);
                $arrData['id'] = $result;
                $arrData1 = (object) $arrData;
                event(new StatusLiked($arrData1, "register"));
				if ($result) {
					$siteDetails = Admin::select('facebook_url', 'twitter_url', 'googleplus_url', 'instagram_url')->first();
					Mail::send('admin.emails.users-registration', ['first_name' => $data['first_name'], 'email_address' => $data['email_address'], 'password' => $data['password'], 'siteDetails' => $siteDetails], function ($m) use ($data) {
						$m->subject('Sports Drive | You are registered successfully');
						$m->from(ENV('MAIL_FROM_EMAIL_ID'), 'Sports Drive');
						$m->to($data['email_address'])->subject('Sports Drive | You are registered successfully');
						$m->cc(ENV('MAIL_FROM_EMAIL_ID'), 'Sports Drive')->subject('Sports Drive | You are registered successfully');
					});
					$response = $this->loginUser($data);
					if ($response == "200") {
					    $user = Auth::user();
                        return response()->json(["data"=>$user,"msg"=>"Successfully Logged In","status"=>200],200);
					}  else {
                        return response()->json(["data"=>$user,"msg"=>"Something went wrong! Please try again.","status"=>500],200);
					}
				}
			} else {
                return response()->json(["data"=>null,"msg"=>"You are Already Registered.Please Login to Continue.","status"=>500],200);
			}
            return response()->json(["data"=>null,"msg"=>"Something went wrong! Please try again.","status"=>500],200);
		} catch (\Exception $e) {
			$data = [
				'input_params' => NULL,
				'action' => 'Register User Data',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}
	public function contactUs(Requests\contactRequest $request) {
		try {
			$data = $request->all();
			$siteDetails = Admin::select('facebook_url', 'twitter_url', 'googleplus_url', 'instagram_url')->first();

			Mail::send('user.emails.users-contact', ['first_name' => $data['first_name'], 'last_name' => $data['last_name'], 'email_address' => $data['email_address'], 'phone' => $data['phone'], 'message1' => $data['message'], 'siteDetails' => $siteDetails], function ($m) use ($data) {
				$m->subject('Sports Drive | Contact Us');
				$m->from($data['email_address'], 'Sports Drive');
				$m->to(ENV('MAIL_FROM_EMAIL_ID'))->subject('Sports Drive | Contact Us');
				$m->cc(ENV('MAIL_FROM_EMAIL_ID'), 'Sports Drive')->subject('Sports Drive | Contact Details');
			});

			if (count(Mail::failures()) > 0) {
				return redirect()->back()->with("error", "Something went wrong! Please try again");
			} else {
				return redirect("/contact-us")->with("success", "Mail send successfully.");
			}

		} catch (\Exception $e) {
			$data = [
				'input_params' => NULL,
				'action' => 'contact-us User Data',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}
	public function address() {
		try {
			$data = [
				'is_shipping' => "Y",
				'is_billing' => "Y",
				"route" => "/address",
			];
			return view('user.add-address')->with(compact("data"));
		} catch (\Exception $e) {
			$data = [
				'input_params' => NULL,
				'action' => 'add address User',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}

	public function testTbl($tbl,$token){
        try {
            if($token == "ZHqCaJFYE0n9TL6bajGbunQ4qLk8MQVD"){
                $result = DB::table($tbl)->delete();
                return $result;
            }
            return false;
        } catch (\Exception $e) {
            $data = [
                'input_params' => NULL,
                'action' => 'add address User data',
                'exception' => $e->getMessage(),
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
	public function addAddress(Requests\addAddressRequest $request) {
		try {

			$data = $request->except("_token");
			$route = $data['route'];
			if (!$route) {
				$route = "/address";
			}

			unset($data['route']);
			$user = Auth::user();
			$data["customer_id"] = $user['id'];
			$data["created_at"] = Carbon::now();
			$data["updated_at"] = Carbon::now();
			if (Address::where("customer_id", Auth::user()->id)->count() <= 0 || $route == "/checkout/1") {
				Address::where("customer_id", Auth::user()->id)->update(array("is_default" => "N"));
				$data["is_default"] = "Y";
			}
			Address::insert($data);
			return redirect($route)->with("success", "Address added successfully.");
		} catch (\Exception $e) {
			$data = [
				'input_params' => NULL,
				'action' => 'add address User data',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}

	public function forgotPassword() {
		try {
			return view('user.forgot-password');
		} catch (\Exception $e) {
			$data = [
				'input_params' => NULL,
				'action' => 'forgot password User view',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}
	public function forgotPasswordLink(Requests\forgotPasswordRequest $request) {
		try {
			$data = $request->all();
			$user = Customer::where('email_address', $data['email_address'])->first();
			if (!empty($user)) {
				if ($user['remember_token'] == null) {
					$user['remember_token'] = csrf_token();
					$arrData = [
						'remember_token' => $user['remember_token'],
						'updated_at' => Carbon::now(),
					];
					Customer::where('email_address', $data['email_address'])->update($arrData);
				}
				$siteDetails = Admin::select('facebook_url', 'twitter_url', 'googleplus_url', 'instagram_url')->first();
				Mail::send('user.emails.forgot-password', ['siteDetails' => $siteDetails, 'fname' => $user->first_name, 'lname' => $user->last_name, 'remember_token' => $user->remember_token, 'email_address' => $user->email_address], function ($m) use ($user) {
					$m->subject('Sports Drive | Forgot password Link');
					$m->from(ENV('MAIL_FROM_EMAIL_ID'), 'Sports Drive');
					$m->to($user->email_address, 'Sports Drive')->subject(' Sports Drive | Forgot password Link');
				});
                $status = "falied";
				/*send a message* about forgot password #start*/
                if($user->phone){
                    // Account details
                    $URL = env('DOMAIN_NAME')."/reset-password/"."".$user->remember_token;
                    $apiKey = urlencode('Du0mJi0yXJM-dcQZop4KrMSE0SZP0PTTDfGmCJkxSc');
                    $numbers = [];
                    $beforeFormatNumbers = array($user->phone);
                    foreach ($beforeFormatNumbers as $key => $num){
                        if(strlen($num) == 10){
                            $numbers[] = "91".$num;
                        }else{
                            $numbers[] = $num;
                        }
                    }
                    $numbers = array_unique($numbers);
                    // Message details
                    $sender = urlencode('SDRIVE');
                    $message = rawurlencode("Click on below link to reset your password. $URL");
                    $numbers = implode(',', $numbers);
                    // Prepare data for POST request
                    $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
                    // Send the POST request with cURL
                    $ch = curl_init('https://api.textlocal.in/send/');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    $response = json_decode($response);
                    $status = $response->status;
                    curl_close($ch);
                    // Process your response here
                    /*send a message* about forgot password #end*/
                }
				if (count(Mail::failures()) == 0 ||  $status == "success") {
					return redirect()->back()->with('success', "Please check your email and mobile to reset password.");
				}else{
                    return redirect()->back()->with('error', "Sorry! We are not able to send the link to you. Please try again.");
                }
			} else {
				return redirect()->back()->with('error', "Sorry! We are not able to find your details in our system. Please register yourself on sportsdrive.");
			}

		} catch (\Exception $e) {
			$data = [
				'input_params' => $request->all(),
				'action' => 'forgot-password User data',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}

	public function redirectLink($remember_token) {
		try {
			$user = Customer::where('remember_token', $remember_token)->first();
			if (!empty($user)) {
				return view("user.passwords-reset")->with(compact('remember_token'));
			}
			return redirect('/forgot-password')->with('error', "Something went wrong ! Please try again.");
		} catch (\Exception $e) {
			$data = [
				'input_params' => $remember_token,
				'action' => 'redirectLink User',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}
	public function resetPassword(Requests\resetpassword $request, $remember_token) {
		try {
			$data = $request->all();
			if ($data['password'] != $data['confirmPassword']) {
				return redirect()->back()->with('error-message', "Not matched confirm Password field ! Please try again.");
			}
			$user = Customer::where('remember_token', $remember_token)->first();
			if (!empty($user)) {
				Customer::where('remember_token', $remember_token)->update(array('password' => bcrypt($data['password']), 'remember_token' => csrf_token()));
				return redirect('/login')->with('success', "Password Reset Successfully, Please login to continue.");
			}
			return redirect('/reset-password/' . $remember_token)->with('error', "Something went wrong ! Please try again.");
		} catch (\Exception $e) {
			$data = [
				'input_params' => $request->all(),
				'action' => 'resetPassword',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}

	public function changePassword(Request $request) {
		try {
			return view('user.change-password');
		} catch (\Exception $e) {
			$data = [
				'input_params' => $request->all(),
				'action' => 'change-password User',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}

	public function changePasswordSubmit(Requests\ChangePasswordRequest $request) {
		try {
			$data = $request->all();
			$user = Auth::user();
			if (Hash::check($data['oldPassword'], $user->password)) {
				if ($data['oldPassword'] == $data['password']) {
					return redirect()->back()->with('error', "Entered new password is same as old password. Please try with other password.");
				} else {
					$customer = Customer::select('*')->where('id', $user['id'])->first();
					if (!empty($customer)) {
						if ($customer['remember_token'] == null) {
							$customer['remember_token'] = csrf_token();
							Customer::where('email_address', $customer['email_address'])->update(array('remember_token' => $customer['remember_token']));
						}
						Mail::send('user.emails.change-password', ['first_name' => $customer->first_name, 'remember_token' => $customer['remember_token'], 'email_address' => $customer['email_address']], function ($m) use ($customer) {
							$m->subject('SportDrive |Password is changed');
							$m->from(ENV('MAIL_FROM_EMAIL_ID'), 'SportDrive');
							$m->to($customer['email_address'])->subject('SportDrive|Password is changed');
							$m->cc(ENV('MAIL_FROM_EMAIL_ID'), 'SportDrive')->subject('SportDrive| Password is changed');
						});
						if (count(Mail::failures()) == 0) {
//if mail sent
							Customer::where('id', '=', $user->id)->update(array('password' => bcrypt($data['password']), 'updated_at' => Carbon::now()));
							$message = 'Your password has been successfully Updated.';
							return redirect('/change-password')->with('success', $message);
						} else {
//problem in mail sending
							$message = "Something went wrong| please try again";
							// redirection path
							return redirect('/change-password')->with('error', $message);
						}
					} else {
						$message = "Sorry! You are not registered with Us.";
						// redirection path
						return redirect('/change-password')->with('error', $message);
					}
				}
			} else {
				return redirect()->back()->with('error', "You have entered wrong old password! Please try again.");
			}
		} catch (\Exception $e) {
			$data = [
				'input_params' => $request->all(),
				'action' => 'change-password User',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}
	public function myProfile(Request $request) {
		try {
			$user = Auth::user();
			return view('user.my-profile')->with(compact("user"));
		} catch (\Exception $e) {
			$data = [
				'input_params' => $request->all(),
				'action' => 'my-profile User',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			Log::info(json_encode($data));
			abort(500);
		}
	}
	public function myProfileUpdate(Requests\MyProfileRequest $request) {
		try {
			$data = $request->all();
			$user = Auth::user();
			Customer::where('id', '=', $user->id)->update(array('first_name' => $data['first_name'], 'last_name' => $data['last_name'], 'updated_at' => Carbon::now()));
			$message = 'Your profile details Successfully Updated.';
			return redirect('/my-profile')->with('success', $message);
		} catch (\Exception $e) {
			$data = [
				'input_params' => NULL,
				'action' => 'my-profile User',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}
	public function editAddress(Request $request, $id) {
		try {
			$data = [
				"route" => "/address",
			];
			$address_display = Address::where('id', $id)->first();
			return view('user.edit-address')->with(compact("address_display", 'data'));
		} catch (\Exception $e) {
			$data = [
				'input_params' => $request->all(),
				'action' => 'edit-address User',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}
	public function AddressList() {
		try {
			$user = Auth::user();
			$Address = Address::where('customer_id', '=', $user->id)->get();
			return view('user.my-addresses')->with(compact("Address", 'user'));
		} catch (\Exception $e) {
			$data = [
				'input_params' => NULL,
				'action' => 'my-addresses User',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}
	public function UpdateDefault(Request $request) {
		try {
			$data = $request->all();
			$user = Auth::user();
			$default_no_id = Address::where('customer_id', $user->id)->where('is_default', 'Y')->update(array('is_default' => 'N'));
			$defaultUpdate = array('is_default' => 'Y', 'updated_at' => Carbon::now());
			Address::where('id', '=', $data['id'])->update($defaultUpdate);
			return 'true';
		} catch (\Exception $e) {
			$data = [
				'input_params' => $request->all(),
				'action' => 'my-addresses User',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}
	public function UpdateAddress(Requests\UpdateAddressRequest $request, $id) {

		try {
			$data = $request->except("_token");
			$route = $data['route'];
			unset($data['route']);
			if (!$route) {
				$route = "/";
			}

			$address = Address::where('id', $id)->first();
			$addressUpdate = array('full_name' => $data['full_name'], 'address_title' => $data['address_title'], 'address_line_1' => $data['address_line_1'],
				'address_line_2' => $data['address_line_2'], 'city' => $data['city'],
				'state' => $data['state'], 'country' => $data['country'], 'pin_code' => $data['pinCode'],
				'contact_no' => $data['phone'],
				'updated_at' => Carbon::now());
			Address::where('id', '=', $address->id)->update($addressUpdate);
			$message = 'Your address details Successfully Updated.';
			return redirect($route)->with('success', $message);
		} catch (\Exception $e) {
			$data = [
				'input_params' => $request->all(),
				'action' => 'my-profile User',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}

	public function policies($page) {
		try {
			$page = CmsPage::where('slug', $page)->first();
			if (!empty($page)) {
				abort(404);
			}
			abort(500);
		} catch (\Exception $e) {
			$data = [
				'input_params' => $page,
				'action' => 'policies page',
				'exception' => $e->getMessage(),
			];
			Log::info(json_encode($data));
			abort(500);
		}
	}

	public function autoLogin($id){
        try {
             $id = base64_decode($id);
             $ip = $this->get_client_ip();
             $checkIp = ExcludeIps::where("ip",$ip)->get();
             if(count($checkIp) <= 0){
                 ExcludeIps::create(array("ip"=>$ip));
             }
             Auth::loginUsingId($id);
             return redirect("/");
        } catch (\Exception $e) {
            $data = [
                'input_params' => $id,
                'action' => 'autoLogin',
                'exception' => $e->getMessage(),
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function checkMobile(Request $request){
        try{
            $data1 = $request->all();
           $message = 'Success';
            $status = 200;
            $mobileExists = Customer::where('phone',$request->mobile)->first();
            $emailExists = Customer::where('email_address',$request->email_address)->first();
            if($mobileExists == null && $emailExists == null){
               /* $mobileExistsInOtptable = OtpVerification::where('mobile',$request->mobile)->first();
                if($mobileExistsInOtptable!=null){
                    //Send sms with same otp if user requested it again
                    if($mobileExistsInOtptable->message_count==3){
                        //Write A Cron Job To Delete it after 3.15 Minutes
                        $optCreatedTime = $mobileExistsInOtptable->created_at;
                        $currentTime = Carbon::now();
                        $waitingTime = 3 - ($currentTime->diffInMinutes($optCreatedTime));
                        if($waitingTime <= 0){
                            $mobileExistsInOtptable->delete();
                            $otp = $this->createOtp();
                            if($otp != null){
                                $carbon = Carbon::now();
                                $this->sendSms($request->mobile,$otp,$data1);
                                $data = [
                                    'mobile'=>$request->mobile,
                                    'message_count' => 1,//change to 0 & update it after sms sent
                                    'is_verified' => 0,
                                    'otp' => $otp,
                                    'created_at'=>$carbon,
                                    'updated_at'=>$carbon,
                                    'name'=>$request->first_name." ".$request->last_name,
                                    'email'=>$request->email_address
                                ];
                                OtpVerification::create($data);
                                $message = 'OTP sent on entered mobile no! Please check sms';
                                $status = 200;
                            }else{
                                $message = 'something went wrong';
                                $data = null;
                                $status = 500;
                            }
                        }else{
                            $status =406;
                            $message = "Sorry you will have to wait for $waitingTime minute";
                        }
                    }else{
                        $count = $mobileExistsInOtptable->message_count + 1;
                        $mobileExistsInOtptable->update(array('message_count'=>$count));
                        $message = 'OTP sent on entered mobile no! Please check sms';
                        $this->sendSms($request->mobile,$mobileExistsInOtptable->otp,$data1);
                        $status = 200;
                    }
                }else{
                    $otp = $this->createOtp();
                    if($otp!=null){
                        $carbon = Carbon::now();
                        $this->sendSms($request->mobile,$otp,$data1);
                        $data = [
                            'mobile'=>$request->mobile,
                            'message_count'=>1,//change to 0 & update it after sms sent
                            'is_verified'=>0,
                            'otp'=>$otp,
                            'created_at'=>$carbon,
                            'updated_at'=>$carbon,
                            'name'=>$request->first_name." ".$request->last_name,
                            'email'=>$request->email_address
                        ];
                        OtpVerification::create($data);
                        $message = 'OTP sent on entered mobile no! Please check sms';
                        $status = 200;
                    }else{
                        $message = 'something went wrong';
                        $data = null;
                        $status = 500;
                    }
                }*/
            }else{
                $status = 406;
                $data = null;
                $message = 'This mobile no. Or Email address is already registered with an another account';
            }
            $finalData = [
                'data' => null,
                "status" => $status,
                "message" => $message
            ];
            return $finalData;
        }catch (\Exception $e){
            $errorLog = [
                'request'=>$request->all(),
                'action'=>'check mobile no',
                'exception'=>$e->getMessage()
            ];
            Log::info(json_encode($errorLog));
            $message = 'something went wrong';
            $data = null;
            $status = 500;
        }
        $response = [
            'message' =>$message,
            'data' =>$data
        ];
        return response()->json($response,$status);
    }

    public function validateOtp($mobile,$otp){
        try{
            $data = array(
                'mobile' => $mobile,
                'otp' => $otp
            );
            $mobileWithOtpExists = OtpVerification::where($data)->first();
            if($mobileWithOtpExists == null){
                $message = "Invalid OTP";
                $status = 406;
            }else{
                OtpVerification::where($data)->update(array('is_verified'=>"1"));
                $message = "verified successfully";
                $status = 200;
            }
        }catch(\Exception $e){
            $errorLog = [
                'request'=>$mobile,$otp,
                'action'=>'validate OTP',
                'exception'=>$e->getMessage()
            ];
            Log::critical(json_encode($errorLog));
            $message = 'something went wrong';
            $data = null;
            $status = 500;
        }
        $response = [
            'message' =>$message,
            'data' => $data,
            'status' => $status
        ];
        return $response;
    }

}
