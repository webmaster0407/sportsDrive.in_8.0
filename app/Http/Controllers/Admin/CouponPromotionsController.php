<?php

namespace App\Http\Controllers\Admin;

use App\Address;
use App\Admin;
use App\coupons;
use App\Customer;
use App\CustomersGroups;
use App\CustomersGroupsUsers;
use App\Jobs\SendEmailToCouponUsers;
use App\Order;
use App\PromotionsCoupons;
use App\PromotionsCouponsUsers;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class CouponPromotionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('adminauth');
    }

    /**
     * Display a listing of the coupons.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $coupons = Coupons::where("is_promotional","Y")->get();
            foreach ($coupons as $key=>$coupon){
                $couponsUsers[$coupon->id] = PromotionsCouponsUsers::where("coupon_id",$coupon['id'])->count();
            }
            return view('admin.coupon-promotions.promotions-coupons')->with(compact("coupons",'couponsUsers'));
        } catch (\Exception $ex) {
            $data = [
                'input_params' => null,
                'action' => 'Admin list Promotional Coupons',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function PromotionSendUsers($id)
    {
        try{
            $coupons = Coupons::where("id",$id)->first();
            $couponsUsers = PromotionsCouponsUsers::where("coupon_id",$coupons->id)->get();
            return view('admin.coupon-promotions.promotion-sent-users')->with(compact("coupons","couponsUsers"));
        } catch (\Exception $ex) {
            $data = [
                'input_params' => null,
                'action' => 'Admin list Promotional Coupons',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request ,$id = null)
    {
        try{
            if($id != null){
                $data = PromotionsCoupons::where("id",$id)->first();
            }else{
                $data = array(
                    "id"=>"0",
                    "name" => "",
                    "code" => "",
                    "discount" => "",
                    "short_description" => "",
                    "mode" => "add",
                    "email_title" => "",
                    "banner_image" => "",
                );
                $data = (object) $data;
                $productIds = array();
            }
            $customers = Customer::where("is_active",'Y')->get();
            $OrderCustomers = Order::where("is_completed","Y")->where("payment_status","9")->pluck("customer_id")->toArray();
            $customersGroup = CustomersGroups::where("is_active","Y")->with("customersGroupsUsers")->get();
            $customers_cities = Address::distinct('city')->pluck("city");
            return view('admin.coupon-promotions.add-promotion-coupon')->with(compact('data',"productIds", "customers","OrderCustomers","customersGroup","customers_cities"));
        } catch (\Exception $ex) {
            $data = [
                'input_params' => $request->all(),
                'action' => 'add Promotional Coupons',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function store(Requests\AddCouponPromotion $request,$id = null) {
        try{
            $data = $request->except("_token");
            $file = $request->file('banner_image');
            if($file){
                $ds = DIRECTORY_SEPARATOR;
                $banner_image_name = uniqid()."".$request->file('banner_image')->getClientOriginalName();
                $destinationPath_icon =  public_path().$ds."uploads".$ds.'coupons_promotions'.$ds;
                if (!file_exists($destinationPath_icon)) {
                    File::makeDirectory($destinationPath_icon, $mode = 0777, true, true);
                }
                $file->move($destinationPath_icon, $banner_image_name);
            }else{
                $banner_image_name= "";
            }
            $time = Carbon::now();
            $coupons = false;
            $couponData = array();
            $discount = $data['discount'];
            if($id == 0){ //add coupons data
                $couponData['name'] = $data['name'];
                $couponData['discount'] = $data['discount'];
                $couponData['short_description'] = $data['short_description'];
                $couponData['is_promotional'] = "Y";
                $couponData['is_active'] = "Y";
                $couponData['email_title'] = $data['email_title'];
                $couponData['banner_image'] = $banner_image_name;
                $couponData['created_at'] = $time;
                $couponData['updated_at'] = $time;
                $coupons = coupons::insertGetId($couponData);
            }
            $data['id'] = $coupons;
            $dateTime = Carbon::now();
            $customers = array();
            if($data['coupons_for'] == 1){//for all customers
                $customers = Customer::where("is_active",'Y')->get();
            }else if($data['coupons_for'] == 2){//for customers who have placed the order
                $orderCustomers = Order::where("is_completed","Y")->where("payment_status","9")->pluck("customer_id")->toArray();
                $customers = Customer::where("is_active",'Y')->whereIn("id",$orderCustomers)->get();
            }else if($data['coupons_for'] == 3){ // for customer groups
                $customerGroupUsers = CustomersGroupsUsers::whereIn("customer_group_id",$data['customers_group'])->pluck("user_id")->toArray();
                $customers = Customer::where("is_active",'Y')->whereIn("id",$customerGroupUsers)->get();
            }else if($data['coupons_for'] == 4){ //for selected customers
                $customers = Customer::where("is_active",'Y')->whereIn("id",$data['all_customers'])->get();
            }else if($data['coupons_for'] == 5){ //for selected cities
                $address = Address::query();
                foreach($data['customers_cities'] as $city){
                    $address->orWhere('city', 'LIKE', '%'.$city.'%');
                }
                $customer_id = $address->distinct()->pluck("customer_id")->toArray();
                $customers = Customer::where("is_active",'Y')->whereIn("id",$customer_id)->get();
            }
            $siteDetails = Admin::select('facebook_url','twitter_url','googleplus_url','instagram_url')->first();
            $couponData = array();
            $dates = array_map('trim',explode("-",$data['valid_till']));
            $dates[0] = Carbon::parse(str_replace('/',"-",$dates[0]));
            $dates[1] = Carbon::parse(str_replace('/',"-",$dates[1]));
            foreach ($customers as $key=>$customer){
                $couponData["coupon_id"] = $data['id'];
                $couponData["email_address"] = $customer->email_address;
                $couponData["mobile_number"] = $customer->phone;
                $couponData["user_id"] = $customer->id;
                $couponData["valid_from"] = $dates[0];
                $couponData["valid_till"] = $dates[1];
                $couponData["discount"] = $data['discount'];
                $couponData["is_used"] = "N";
                $couponData['code'] = $customer->phone;
                $couponData["created_at"] = $dateTime;
                $couponData["updated_at"] = $dateTime;
                $customer->siteDetails = $siteDetails;
                $customer->banner_image_name = $banner_image_name;
                $customer->discount = $discount;
                dispatch(new SendEmailToCouponUsers($couponData,$customer->toArray()));
            }
            $message = "Coupon has been created successfully for selected customers and ".count($customers)." customers soon customers will receive the email for same.";
            return redirect("/administrator/list-coupon-promotions")->with('success', $message);
        } catch (\Exception $ex) {
            $data = [
                'input_params' => $request->all(),
                'action' => 'Admin store Coupons',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);

        }
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeCouponsStatus(Request $request ,$id = null) {
        try{
            $data = $request->all();
            $operationFlag = $data['operationFlag'];
            $couponIds = $data['chk'];
            $message = "Something went wrong!Please try again";
            if ($operationFlag == 'active') {
                coupons::whereIn('id', $couponIds)->update(array('is_active' => 'Y'));
                $message = "Coupons's successfully activated.";
            } else if ($operationFlag == 'deactive') {
                coupons::whereIn('id', $couponIds)->update(array('is_active' => 'N'));
                $message = "Coupons's successfully deactivated.";
            } else if ($operationFlag == 'delete') {
                coupons::whereIn('id', $couponIds)->delete();
                $message = "Coupons's successfully deleted.";
            }
            return redirect("/administrator/list-coupon-promotions")->with('success', $message);
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'Admin change promotions Coupons Status',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function sendAgain($id){
        try{
            $siteDetails = Admin::select('facebook_url','twitter_url','googleplus_url','instagram_url')->first();
            $coupon = coupons::find($id);
            $dateTime = Carbon::now();
            $alreadySent = PromotionsCouponsUsers::where("coupon_id",$id)->pluck("user_id")->toArray();
            $customers = Customer::where("is_active","Y")->whereNotIn("id",$alreadySent)->get();
            $success = 0;
            foreach ($customers as $key=>$customer){
                $couponData["coupon_id"] = $id;
                $couponData["email_address"] = $customer->email_address;
                $couponData["mobile_number"] = $customer->phone;
                $couponData["user_id"] = $customer->id;
                $couponData["valid_from"] = "2019-04-16";
                $couponData["valid_till"] = "2019-04-30";
                $couponData["discount"] = $coupon->discount;
                $couponData["is_used"] = "N";
                $couponData['code'] = $customer->phone;
                $couponData["created_at"] = $dateTime;
                $couponData["updated_at"] = $dateTime;
                $customer->siteDetails = $siteDetails;
                $customer->banner_image_name = $coupon->banner_image;
                $customer->discount = $coupon->discount;
                $success++;
                dispatch(new SendEmailToCouponUsers($couponData,$customer->toArray()));
            }
            dd("emails send to $success users");
        }catch(\Exception $e){
            $data = [
            'input_params' => $id,
            'action' => 'Admin change promotions Coupons Status',
            'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
            }
    }
}
