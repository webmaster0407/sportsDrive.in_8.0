<?php

namespace App\Http\Controllers;

use App\Address;
use App\Cart;
use App\coupons;
use App\CouponsProducts;
use App\CouponsUsers;
use App\Customer;
use App\Http\Controllers\CustomTraits\LoginTraits;
use App\Http\Controllers\CustomTraits\VisitorsTrait;
use App\Offers;
use App\Product;
use App\ProductConfiguration;
use App\Http\Controllers\CustomTraits\CartTrait;
use App\Http\Controllers\CustomTraits\OrderTrait;
use App\Order;
use App\ProductsOffers;
use App\PromotionsCouponsUsers;
use App\StatusMaster;
use App\OrderReturn;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;
use App\RatingReviews;
use DB;
use Seshac\Shiprocket\Shiprocket;

class OrderController extends Controller {

    use CartTrait;
    use OrderTrait;
    use VisitorsTrait;
    use LoginTraits;

    /**
     * OrderController constructor.
     */
    public function __construct() {
        $this->middleware('userauth');
        $this->middleware('checkVisitors');
    }

    /**
     * @param Requests\GenerateOrder $request
     * @param $order_id
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function generateOrder(Requests\GenerateOrder $request, $order_id) {
        try {
            $order = null;
            $order_id = base64_decode($order_id);
            $order = Order::where("id", $order_id)->first();
            $pendingOrderStatusId = StatusMaster::where('slug', 'pending')->where('is_order', 'Y')->pluck('status_id');
            $orderStatus = $order['order_status'];
            if ($order == null || $order['is_completed'] != "Y" || $order['order_status'] != $pendingOrderStatusId[0])
                return redirect("/cart/view");
            $failedOrderStatus = StatusMaster::where('slug', 'failed')->where('is_order', 'Y')->pluck('status_id');
            if($orderStatus == $failedOrderStatus[0]){
                $order_id = base64_encode($order_id);
                return redirect("/order/failed/$order_id");
            }

            /*send sms code starts*/
            $userShownOrderId = $this->getOrderId($order_id);
// Account details
            $apiKey = urlencode('Du0mJi0yXJM-dcQZop4KrMSE0SZP0PTTDfGmCJkxSc');
            $shipping_address = json_decode($order->shipping_address);
            $billing_address = json_decode($order->billing_address);
            $user = User::where("id",$order['customer_id'])->first();
            $beforeFormatNumbers[] = $shipping_address->contact_no;
            $beforeFormatNumbers[] = $billing_address->contact_no;
            $beforeFormatNumbers[] = $user->phone;
            $numbers = [];
            foreach ($beforeFormatNumbers as $key => $num){
                if(strlen($num) == 10){
                    $numbers[] = "91".$num;
                }else{
                    $numbers[] = $num;
                }
            }
            $numbers = array_unique($numbers);
            // Message details
            $sender = urlencode('SDrive');
            $amount = intval($order->sub_total+$order->total_shipping_amount);
            $message = rawurlencode("Order Received: Your www.SportsDrive.in ORDER #$userShownOrderId for Rs. $amount has been received.");
            $numbers = implode(',', $numbers);
            // Prepare data for POST request
            $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
            // Send the POST request with cURL
            $ch = curl_init('https://api.textlocal.in/send/');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            // Process your response here
            /*send sms code ends*/

            $userShownOrderId = $this->getOrderId($order->id);
            $order = Order::where("id", $order->id)->first();
            $carts = Cart::where("order_id", $order->id)->where("is_deleted", "N")->get();
            return view("user.order-success")->with(compact("order", "carts", "userShownOrderId"));
        } catch (\Exception $e) {
            $data = [
                'input_params' => $request->all(),
                'action' => 'generateOrder',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function paymentRequest(Request $request, $order_id) {
        try {
            $data = $request->all();
            $amountDetails = null;
            $data["code"] = $data['applied_code'];
            $request->merge($data);
            // if coupon is applied then get coupon discount
            if($data['code'] !== null){
                $amountDetails = $this->coupon($request);;
            }
            $data['delivery'] = "express";
            $user = Auth::user();
            $order = null;
            $order_id = base64_decode($order_id);
            $order = Order::where("id", $order_id)->first();
            if ($order === null)
                return redirect("/cart/view");
            if($order->is_payment_proceed == "Y"){//if previous order is proceeded to gateway and we did not received any response
                $carts = Cart::where("order_id", $order_id)
                    ->where("is_deleted", "N")
                    ->get()
                    ->toArray();
                //update previous order
                $failedPaymentStatus = StatusMaster::where('slug', 'failed')->where('is_payment', 'Y')->pluck('status_id');
                $failedOrderStatus = StatusMaster::where('slug', 'failed')->where('is_order', 'Y')->pluck('status_id');
                $updateData["order_status"] = $failedOrderStatus[0];
                $updateData["payment_status"] = $failedPaymentStatus[0];
                $updateData["is_completed"] = "Y";
                $updateData["is_abandoned"] = "Y";
                Order::where("id",$order_id)->update($updateData);
                //create a new order
                unset($order->id);
                $order = $order->toArray();
                $order['is_payment_proceed'] = "N";
                $order_id = Order::insertGetId($order);
                //create all new carts
                 foreach ($carts as $cart){
                     unset($cart['id']);
                     $cart['order_id'] = $order_id;
                     Cart::insert($cart);
                 }
                session()->put("order_id",$order_id);
                $order = Order::where("id", $order_id)->first();
            }

            $cartData = $this->getUserCart();
            $cartData = $this->getAttributeDataFromCartData($cartData); // color id and size id will be replace by its name
            $offersPrices = $this->getOffersProductPrice();
            if (isset($offersPrices['finalDiscountedAmount'])) {
                $subtotal = $offersPrices['finalDiscountedAmount'];    
            } else {
                $subtotal = 0;    
            }

            if (isset($offersPrices['finalDiscount'])) {
                $totalDiscount = $offersPrices['finalDiscount']; 
            } else {
                $totalDiscount = 0;    
            }

            if($amountDetails != null && $amountDetails['status'] == 200){
                $subtotal =$subtotal-$amountDetails['additionalDiscount'];
            }

            /* calculate shipping charge */

            if (isset($offersPrices['totalShippingCharge'])) {
                $shippingCharge = $offersPrices['totalShippingCharge'];
            } else {
                $shippingCharge = 0;    
            }

            
            if (!(array_key_exists('billing_address_id', $data))) {
                $data['billing_address_id'] = $data['shipping_address_id'];
            }
            //update order data
            $shippingAddress = Address::where("id", $data['shipping_address_id'])->first();
            $billingAddress = Address::where("id", $data['billing_address_id'])->first();
            $pendingOrderStatusId = StatusMaster::where('slug', 'pending')->where('is_order', 'Y')->pluck('status_id');
            $pendingPaymentStatusId = StatusMaster::where('slug', 'pending')->where('is_payment', 'Y')->pluck('status_id');
            $orderData = [
                'sub_total' => $subtotal,
                'discount' => $totalDiscount,
                'total' => number_format($subtotal + $shippingCharge, 2, '.', ''), //$subtotal+$shippingCharge,
                'total_shipping_amount' => $shippingCharge,
                'shipping_address' => json_encode($shippingAddress),
                'billing_address' => json_encode($billingAddress),
                'order_status' => $pendingOrderStatusId[0],
                'payment_status' => $pendingPaymentStatusId[0],
                'payment_mode' => $data['payment'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'is_completed' => 'Y',
                "is_payment_proceed"=>"Y",
                "is_payment_proceed_on" => Carbon::now(),
                "customer_id" => $user->id
            ];

            if($data['code'] !== null && $amountDetails['status'] == 200){
                $orderData["coupon_discount"] = $amountDetails['additionalDiscount'];
                $orderData["coupon_code"] = $data['code'];
                $coupon = coupons::where("code",$data['code'])->first();
                $is_promotional = "N";
                if(count($coupon) == 0){
                    $promotionCoupon = PromotionsCouponsUsers::where("code",$data['code'])->first();
                    $coupon = coupons::where("id",$promotionCoupon->coupon_id)->first();
                    $is_promotional = "Y";
                }
                //insert the entry into coupon users table
                $couponUsersData = [
                    'coupon_id'=>$coupon->id,
                    'user_id' => $user->id,
                    'order_id' => $order_id,
                    'is_promotional' => $is_promotional,
                    'created_at' =>Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                CouponsUsers::insert($couponUsersData);
            }
            Order::where('id', $order->id)->update($orderData);
            $cartProductName = "";
           
            //update cart data
            foreach ($cartData as $cart) {
                //check color and sie is not null
                $color = null;
                $size = null;

                if (is_countable($cart->colorId) && count($cart->colorId) > 0) {
                    $color = $cart->color[0];
                }
                if (is_countable($cart->sizeId) && count($cart->sizeId) > 0) {
                    $size = $cart->size[0];
                }
                if ($cart->configuration_id == null)//if there is no conf. for product then take main price
                    $cart->mainPrice = $cart->price;
                if ($cart->image != null)
                    $image = $cart->image;
                else
                    $image = $cart->mainImage;
                $cartInfo = [
                    'configuration_image' => $image,
                    'product_slug' => $cart->slug,
                    'product_name' => $cart->name,
                    'color' => $color,
                    'size' => $size,
                    'price_per_qty' => $cart->mainPrice,
                    'discount_per_qty' => $cart->discount_price,
                    'final_price' => ($cart->mainPrice - $cart->discount_price) * $cart->quantity,
                    'updated_at' => Carbon::now(),
                ];
                Cart::where('id', $cart->id)->update($cartInfo);
                $cartProductName .=$cart->name;
            }
            $hash_string = '';
            //$cartProductName = substr($cartProductName, 0, 250);
            //initialise payment request
            $posted = array();
            $SALT = ENV('PayU_SALT');
            $posted['key'] = $key = ENV('PayU_KEY');
            $posted['txnid'] = $txnid = $order->id;
            $posted['amount'] = $amount = number_format($subtotal + $shippingCharge, 2, '.', '');
            $posted['productinfo'] = $productinfo = $cartProductName;
            $posted['firstname'] = $firstname = $user->first_name;
            $posted['lastname'] = $lastname = $user->last_name;
            $posted['email'] = $email = $user->email_address;
            $posted['phone'] = $phone = $user->phone;
            $posted['udf1'] = $udf1 = $cartProductName;
            $posted['udf2'] = $udf2 = $email;
            $posted['udf3'] = $udf3 = $phone;
            $posted['udf4'] = $udf4 = $shippingAddress['address_line_1']." ".$shippingAddress['address_line_2']." ".$shippingAddress['city']." ".$shippingAddress['state']." ".$shippingAddress['country']." ".$shippingAddress['pin_code'];
            $posted['udf5'] = $udf5 = $txnid;
            $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|||||";
            if ( empty($posted['key']) 
                || empty($posted['txnid']) 
                || empty($posted['amount']) 
                || empty($posted['firstname']) 
                || empty($posted['email']) 
                || empty($posted['phone']) 
                || empty($posted['productinfo'])
            )  {
                $url = "/order/failed/"."".base64_encode($order_id);
                return redirect("$url");
            } else {
                $hashVarsSeq = explode('|', $hashSequence);
                foreach ($hashVarsSeq as $hash_var) {
                    $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
                    $hash_string .= '|';
                }
                $hash_string .= $SALT;
                $hash = strtolower(hash('sha512', $hash_string));
                $dateCurrent = date("Y-m-d H:i:s");
                $fp = fopen('payment_log/hdfcPaymentLog.txt', 'a+');
                fwrite($fp, "============================$dateCurrent=========================\n\n");
                fwrite($fp, "Request\n");
                fwrite($fp, print_r($hash_string, true));
                fwrite($fp, "\r\n\r\n");
                fwrite($fp, print_r($posted, true));
                fwrite($fp, "\r\n\r\n");
                fwrite($fp, print_r($hash, true));
                fwrite($fp, "\r\n\r\n");
                fclose($fp);
                $action = ENV('PAYU_BASE_URL') . '/_payment';
            }
            //Trigger payment proceed event
            $user->cartCount = count($cartData);
            $user->subTotal = $offersPrices['finalDiscountedAmount'] + $offersPrices['totalShippingCharge'];
            $this->StoreNotificationData($user, "proceed_payment");
            return view('user.payu-request-form')
                ->with(
                    compact(
                        "hash", 
                        "posted", 
                        "action", 
                        "shippingAddress"
                    )
                );
        } catch (Exception $ex) {
            $data = [
                'input_params' => $request->all(),
                'action' => 'payment Request',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function paymentResponse(Request $request) {
        try {
            $user = Auth::user();
            $status = $_POST["status"];
            $firstname = $_POST["firstname"];
            $txnid = $_POST["txnid"];
            $amount = $_POST["amount"]; //Please use the amount value from database
            $amountFromDb = Order::where('id', $txnid)->pluck('total');
            $amount = number_format($amountFromDb[0], 2, '.', '');
            $posted_hash = $_POST["hash"];
            $key = $_POST["key"];
            $productinfo = $_POST["productinfo"];
            $email = $_POST["email"];
            $udf1 = $_POST["udf1"];
            $udf2 = $_POST["udf2"];
            $udf3 = $_POST["udf3"];
            $udf4 = $_POST["udf4"];
            $udf5 = $_POST["udf5"];
            $salt = ENV('PayU_SALT'); //Please change the value with the live salt for production environment
            $dateCurrent = date("Y-m-d H:i:s");
            $fp = fopen('payment_log/hdfcPaymentLog.txt', 'a+');
            fwrite($fp, "============================$dateCurrent=========================\n\n");
            fwrite($fp, "Response\n");
            fwrite($fp, print_r($_POST, true));
            fwrite($fp, "\r\n\r\n");
            fclose($fp);
            //Validating the reverse hash
            if (isset($_POST["additionalCharges"])) {
                $additionalCharges = $_POST["additionalCharges"];
                $retHashSeq = $additionalCharges . '|' . $salt . '|' . $status . '||||||' . $udf5 . '|' . $udf4 . '|' . $udf3 . '|' . $udf2 . '|' . $udf1 . '|' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
            } else {
                $retHashSeq = $salt . '|' . $status . '||||||' . $udf5 . '|' . $udf4 . '|' . $udf3 . '|' . $udf2 . '|' . $udf1 . '|' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
            }
            $hash = hash("sha512", $retHashSeq);
            /*
             * PayU Parameter For DB
             */
            if (trim($status) == "success") {
                $paymentStatus = StatusMaster::where('slug', 'completed')->where('is_payment', 'Y')->pluck('status_id');
            } else {
                $paymentStatus = StatusMaster::where('slug', 'failed')->where('is_payment', 'Y')->pluck('status_id');
            }
            if(!array_key_exists("cardCategory",$_POST))
                $_POST['cardCategory'] = null;
            $PayU_PaymentID = $_POST['mihpayid'];
            $PayU_PaymentMode = $_POST['mode'];
            $payU_cardCategory = $_POST['cardCategory'];
            $payU_net_amount_debit = $_POST['net_amount_debit'];
            $payU_PG_TYPE = $_POST['PG_TYPE'];
            $payU_Error_Message = $_POST['error_Message'];
            $payU_bank_ref_num = $_POST['bank_ref_num'];
            $useragent = $_SERVER ['HTTP_USER_AGENT'];
            $remoteIPaddr = $_SERVER['REMOTE_ADDR'];
            $orderUpdateData = [
                "order_date" => Carbon::now(),
                "payment_status" => $paymentStatus[0],
                "is_completed" => "Y",
                "payu_payment_id" => $PayU_PaymentID,
                "payu_payment_mode" => $PayU_PaymentMode,
                "payu_card_category" => $payU_cardCategory,
                "payu_net_amount_debit" => $payU_net_amount_debit,
                "payu_pg_type" => $payU_PG_TYPE,
                "payu_bank_ref_num" => $payU_bank_ref_num,
                "user_agent" => $useragent,
                "ip_address" => $remoteIPaddr,
                "payment_custom_message"=>$payU_Error_Message
            ];
            $offersPrices = $this->getOffersProductPriceFromOrderId($txnid);
            if (isset($offersPrices['finalDiscountedAmount'])) {
                $subtotal = $offersPrices['finalDiscountedAmount'];    
            } else {
                $subtotal = 0;    
            }
            $order =  Order::where('id', $txnid)->first();
            //if coupon is applied then checking the datails
            $amountDetails = null;
            if($order['coupon_code'] != null){
                $data['code']  = $order['coupon_code'];
                $data['order_id']  = base64_encode($txnid);
                $data['from_order_response']  = 1;
                $request->merge($data);
                $amountDetails = $this->coupon($request,1);;
            }
            if($amountDetails != null && $amountDetails['status'] == 200){
                $subtotal = $subtotal - $amountDetails['additionalDiscount'];
            }
            $temperedFlag = 0;
            if($payU_net_amount_debit != ($subtotal+$offersPrices['totalShippingCharge']))
                $temperedFlag = 1;
            Order::where('id', $txnid)->update($orderUpdateData);
            $order =  Order::where("id",$txnid)->first();
            $encriptOrderId = base64_encode($txnid);
            if (trim(strtolower($status)) != "success") { // for Failed
                $orderStatus = StatusMaster::where('slug', 'failed')->where('is_order', 'Y')->pluck('status_id');
                $orderFailedUpdate = [
                    "order_status" => $orderStatus[0],
                ];
                Order::where('id', $txnid)->update($orderFailedUpdate);
                $message = "Transaction has been failed. Please try again.";
                return redirect("order/failed/$encriptOrderId")->with('message', $message);
            } else if ($hash != $posted_hash || $temperedFlag == 1) { // for tempering Data
                $message = "Transaction has been tampered. Please try again.";
                $orderStatus = StatusMaster::where('slug', 'failed')->where('is_order', 'Y')->pluck('status_id');
                $orderFailedUpdate = [
                    "order_status" => $orderStatus[0],
                    "payment_custom_message" => $message
                ];
                Order::where('id', $txnid)->update($orderFailedUpdate);
                return redirect("order/failed/$encriptOrderId")->with('message', $message);
            } else {//Order success
                /*check if coupon is created as coupon promotions #start*/
                $couponUsers  = CouponsUsers::where("order_id",$txnid)->orderBy('created_at', 'desc')->first();
                if($couponUsers != null){
                    $coupon = coupons::find($couponUsers->coupon_id);
                    if($coupon->is_promotional == "Y"){
                        PromotionsCouponsUsers::where("coupon_id",$coupon->id)->where("user_id",$user->id)->update(array("is_used"=>"Y"));
                    }
                }
                /*check if coupon is created as coupon promotions* #end*/
                $user = Auth::user();
                $userShownOrderId = $this->getOrderId($order->id);
                $order = Order::where("id", $order->id)->first();
                $shipping_address_details = json_decode($order->shipping_address);
                $billing_address_details = json_decode($order->billing_address);
                $carts = Cart::where("order_id", $order->id)->where("is_deleted", "N")->get();
                foreach ($carts as $key=>$cart){
                    //reduce the qty of config or product
                    if($cart['configuration_id']!=null){
                        $qty = ProductConfiguration::where("id",$cart['configuration_id'])->pluck("quantity");
                        $qty = $qty[0]-$cart['quantity'];
                        ProductConfiguration::where("id",$cart['configuration_id'])->update(array("quantity"=>$qty));
                    }
                    //reduce product qty
                    $productQty = Product::where("id",$cart->product_id)->pluck("quantity");
                    $productQty = $productQty[0]-$cart['quantity'];
                    Product::where("id",$cart->product_id)->update(array("quantity"=>$productQty));
                    //get SKU
                    $carts[$key]['sku'] = Product::where("id",$cart->product_id)->pluck("sku")->toArray();
                }
                $amount = intval($order->sub_total+$order->total_shipping_amount);
                $emailMessage = "Order Received: Your www.SportsDrive.in ORDER #$userShownOrderId for Rs. $amount has been received.";
                Mail::send('user.emails.order_confirmation_email', ['userShownOrderId' => $userShownOrderId, 'order' => $order, 'cart' => $carts, 'shipping_address_details' => $shipping_address_details, 'billing_address_details' => $billing_address_details,'emailMessage'=>$emailMessage], function ($m) use ($user,$userShownOrderId) {
                    $m->subject('SportsDrive.In | Payment & Order Confirmation'."#".$userShownOrderId);
                    $m->from(ENV('ADMIN_ORDER_EMAIL_ID'), 'SportsDrive.in');
                    $m->to($user['email_address'])->subject('SportsDrive.in | Payment & Order Confirmation'."#".$userShownOrderId);
                    $m->cc(ENV('ADMIN_ORDER_EMAIL_ID'), 'SportsDrive.in')->subject('SportsDrive.in | Payment & Order Confirmation '." #".$userShownOrderId);
                });
                $encriptOrderId = base64_encode($txnid);
                return redirect("/order/order_success/$encriptOrderId");
            }
        } catch (Exception $ex) {
            $data = [
                'input_params' => $_POST,
                'action' => 'Payment Response',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public static function paymentResponseCron(Request $request) {
        try {
            $_POST = $request->all();
            $status = $_POST["status"];
            $firstname = $_POST["firstname"];
            $txnid = $_POST["txnid"];
            $amount = $_POST["amt"]; //Please use the amount value from database
            $amountFromDb = Order::where('id', $txnid)->pluck('total');
            $amount = number_format($amountFromDb[0], 2, '.', '');
            $productinfo = $_POST["productinfo"];
            $udf1 = $_POST["udf1"];
            $udf2 = $_POST["udf2"];
            $udf3 = $_POST["udf3"];
            $udf4 = $_POST["udf4"];
            $udf5 = $_POST["udf5"];
            $salt = ENV('PayU_SALT'); //Please change the value with the live salt for production environment
            $dateCurrent = date("Y-m-d H:i:s");
            /*$fp = fopen('payment_log/hdfcPaymentLog.txt', 'w+');
            fwrite($fp, "============================$dateCurrent=========================\n\n");
            fwrite($fp, "Response\n");
            fwrite($fp, print_r($_POST, true));
            fwrite($fp, "\r\n\r\n");
            fclose($fp);*/
            /*
             * PayU Parameter For DB
             */
            if (trim($status) == "success") {
                $paymentStatus = StatusMaster::where('slug', 'completed')->where('is_payment', 'Y')->pluck('status_id');
                 $orderStatus = StatusMaster::where('slug', 'pending')->where('is_order', 'Y')->pluck('status_id');
            } else {
                $paymentStatus = StatusMaster::where('slug', 'failed')->where('is_payment', 'Y')->pluck('status_id');
                 $orderStatus = StatusMaster::where('slug', 'failed')->where('is_order', 'Y')->pluck('status_id');
            }
            if(!array_key_exists("cardCategory",$_POST))
                $_POST['cardCategory'] = null;
                $PayU_PaymentID = $_POST['mihpayid'];
                $PayU_PaymentMode = $_POST['mode'];
                $payU_cardCategory = $_POST['cardCategory'];
                $payU_net_amount_debit = $_POST['net_amount_debit'];
                $payU_PG_TYPE = $_POST['PG_TYPE'];
                $payU_Error_Message = $_POST['error_Message'];
                $payU_bank_ref_num = $_POST['bank_ref_num'];
                //$useragent = $_SERVER ['HTTP_USER_AGENT'];
               // $remoteIPaddr = $_SERVER['REMOTE_ADDR'];
                $orderUpdateData = [
                    "order_date" => Carbon::now(),
                    "payment_status" => $paymentStatus[0],
                    "order_status" => $orderStatus[0],
                    "is_completed" => "Y",
                    "payu_payment_id" => $PayU_PaymentID,
                    "payu_payment_mode" => $PayU_PaymentMode,
                    "payu_card_category" => $payU_cardCategory,
                    "payu_net_amount_debit" => $payU_net_amount_debit,
                    "payu_pg_type" => $payU_PG_TYPE,
                    "payu_bank_ref_num" => $payU_bank_ref_num,
                    "user_agent" => NULL,
                    "ip_address" => NULL,
                    "payment_custom_message"=>$payU_Error_Message
                ];
            $offersPrices = OrderController::getOffersProductPriceFromOrderIdron($txnid);
            if (isset($offersPrices['finalDiscountedAmount'])) {
                $subtotal = $offersPrices['finalDiscountedAmount'];    
            } else {
                $subtotal = 0;    
            }
            $order =  Order::where('id', $txnid)->first();
            //if coupon is applied then checking the datails
            $amountDetails = null;
            if($order['coupon_code'] != null){
                $data['code']  = $order['coupon_code'];
                $data['order_id']  = base64_encode($txnid);
                $data['from_order_response']  = 1;
                $request->merge($data);
                $amountDetails = OrderController::coupon($request,1);;
            }
            if($amountDetails != null && $amountDetails['status'] == 200){
                $subtotal = $subtotal - $amountDetails['additionalDiscount'];
            }
            $temperedFlag = 0;
            if($payU_net_amount_debit != ($subtotal+$offersPrices['totalShippingCharge']))
                $temperedFlag = 1;
            Order::where('id', $txnid)->update($orderUpdateData);
            $order =  Order::where("id",$txnid)->first();
            $encriptOrderId = base64_encode($txnid);
            if (trim(strtolower($status)) != "success") { // for Failed
                $orderStatus = StatusMaster::where('slug', 'failed')->where('is_order', 'Y')->pluck('status_id');
                $orderFailedUpdate = [
                    "order_status" => $orderStatus[0],
                ];
                Order::where('id', $txnid)->update($orderFailedUpdate);
                return 404;
            } else if ($temperedFlag == 1) { // for tempering Data
                $message = "Transaction has been tampered. Please try again.";
                $orderStatus = StatusMaster::where('slug', 'failed')->where('is_order', 'Y')->pluck('status_id');
                $orderFailedUpdate = [
                    "order_status" => $orderStatus[0],
                    "payment_custom_message" => $message
                ];
                Order::where('id', $txnid)->update($orderFailedUpdate);
                return 404;
            } else {//Order success
                 $orderStatus = StatusMaster::where('slug', 'pending')->where('is_order', 'Y')->pluck('status_id');
                $orderSucessUpdate = [ "order_status" => $orderStatus[0]];
                Order::where('id', $txnid)->update($orderSucessUpdate);
                /*check if coupon is created as coupon promotions #start*/
                $couponUsers  = CouponsUsers::where("order_id",$txnid)->orderBy('created_at', 'desc')->first();
                if($couponUsers != null){
                    $coupon = coupons::find($couponUsers->coupon_id);
                    if($coupon->is_promotional == "Y"){
                        PromotionsCouponsUsers::where("coupon_id",$coupon->id)->where("user_id",$user->id)->update(array("is_used"=>"Y"));
                    }
                }
                /*check if coupon is created as coupon promotions* #end*/
                $userShownOrderId = OrderController::getOrderId($order->id);
                $order = Order::where("id", $order->id)->first();
                $user = Customer::find($order->customer_id);
                $shipping_address_details = json_decode($order->shipping_address);
                $billing_address_details = json_decode($order->billing_address);
                $carts = Cart::where("order_id", $order->id)->where("is_deleted", "N")->get();
                foreach ($carts as $key=>$cart){
                    //reduce the qty of config or product
                    if($cart['configuration_id']!=null){
                        $qty = ProductConfiguration::where("id",$cart['configuration_id'])->pluck("quantity");
                        $qty = $qty[0]-$cart['quantity'];
                        ProductConfiguration::where("id",$cart['configuration_id'])->update(array("quantity"=>$qty));
                    }
                    //reduce product qty
                    $productQty = Product::where("id",$cart->product_id)->pluck("quantity");
                    $productQty = $productQty[0]-$cart['quantity'];
                    Product::where("id",$cart->product_id)->update(array("quantity"=>$productQty));
                    //get SKU
                    $carts[$key]['sku'] = Product::where("id",$cart->product_id)->pluck("sku")->toArray();
                }
                $amount = intval($order->sub_total+$order->total_shipping_amount);
                $emailMessage = "Order Received: Your www.SportsDrive.in ORDER #$userShownOrderId for Rs. $amount has been received.";
                Mail::send('user.emails.order_confirmation_email', ['userShownOrderId' => $userShownOrderId, 'order' => $order, 'cart' => $carts, 'shipping_address_details' => $shipping_address_details, 'billing_address_details' => $billing_address_details,'emailMessage'=>$emailMessage], function ($m) use ($user,$userShownOrderId) {
                    $m->subject('SportsDrive.In | Payment & Order Confirmation'."#".$userShownOrderId);
                    $m->from(ENV('ADMIN_ORDER_EMAIL_ID'), 'SportsDrive.in');
                    $m->to($user['email_address'])->subject('SportsDrive.in | Payment & Order Confirmation'."#".$userShownOrderId);
                    $m->cc(ENV('ADMIN_ORDER_EMAIL_ID'), 'SportsDrive.in')->subject('SportsDrive.in | Payment & Order Confirmation '." #".$userShownOrderId);
                });
                return 200;
            }
        } catch (Exception $ex) {
            $data = [
                'input_params' => $_POST,
                'action' => 'Payment Response Cron',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            return 404;
        }
    }

    public static function getOrderId($orderId){
        $orderIdLength = strlen((string)$orderId);
        $requiredOrderIdLength = ENV('ORDERIDLENGHT1');
        $totalZeroesRequired = $requiredOrderIdLength-$orderIdLength;
        $string = null;
        for ($i = 0;$i<$totalZeroesRequired;$i++)
            $string= $string."0";
        $userShownOrderId = "OD".$string.$orderId;
        return $userShownOrderId;
    }

    public static function getOffersProductPriceFromOrderIdron($order_id){
        $order = Order::where("id",$order_id)->first();
        if($order!=null){
            $allProductContainsOfferFinalArray = array();
            $allProductsIds = Cart::where("order_id",$order->id)
                ->where('is_deleted',"N")
                ->pluck("product_id")->toArray();

            //get all offers related to cart
            $offers = ProductsOffers::join("offers","products_offers.offer_id","=","offers.id")->where("offers.is_active","Y")->whereIn("product_id",$allProductsIds)->groupBy("products_offers.offer_id")->select("products_offers.*")->get();
            $subtotalAfterDiscount = 0;
            $finalDiscount = 0;
            $totalDiscount = 0;
            $totalShippingCharge = 0;
            $offerFlag = 0;
            /*this is loop for products which contains offers*/
            foreach ($offers as $key=>$offer){
                $offerFlag = 1;
                $allProductContainsOffer = ProductsOffers::whereIn("product_id",$allProductsIds)
                    ->where("offer_id","=",$offer['offer_id'])
                    ->pluck("product_id")->toArray();
                $allProductContainsOfferFinalArray = array_merge($allProductContainsOfferFinalArray,$allProductContainsOffer);
                $offer = Offers::where("id",$offer['offer_id'])->first();
                $cartsContainsThisParticularOffer = Cart::where("order_id",$order->id)
                    ->where('is_deleted',"N")
                    ->whereIn('product_id',$allProductContainsOffer)->get();
                $config_ids = array_column($cartsContainsThisParticularOffer->toArray(), 'configuration_id');
                $configurationsEligibleForOfferWithSameSize = array();
                foreach($config_ids as $ids){
                    $AttributeSize = ProductConfiguration::where("id",$ids)->pluck("AttributeSize");
                    $sameConfigurations = ProductConfiguration::whereIn("id",$config_ids)->where("AttributeSize",$AttributeSize[0])->pluck("id")->toArray();
                    $offerApplicableCartsItems = Cart::whereIn("configuration_id",$sameConfigurations)->where("order_id",$order_id)->where("is_deleted","N")->sum("quantity");
                    if($offerApplicableCartsItems>1){
                        $carts = Cart::whereIn("configuration_id",$sameConfigurations)->where("order_id",$order_id)->where("is_deleted","N")->pluck("id")->toArray();
                        $configurationsEligibleForOfferWithSameSize = array_unique(array_merge($configurationsEligibleForOfferWithSameSize,$carts));
                    }
                }
                $cartsEligibleForOfferWithSameSize = Cart::where("order_id",$order->id)
                    ->where('is_deleted',"N")
                    ->whereIn('id',$configurationsEligibleForOfferWithSameSize)
                    ->whereIn('product_id',$allProductContainsOffer)
                    ->pluck("id")->toArray();
                $cartsNotEligibleForOfferWithSameSize = array_diff(array_column($cartsContainsThisParticularOffer->toArray(),"id"),$cartsEligibleForOfferWithSameSize);
                $totalApplicableCartQuantity = Cart::whereIn("id",$cartsEligibleForOfferWithSameSize)->sum("quantity");
                $cartData = Order::join('carts','carts.order_id','=','orders.id')
                    ->join('products','products.id','=','carts.product_id')
                    ->leftjoin('product_configuration','product_configuration.id','=','carts.configuration_id')
                    ->whereIn('carts.id',$cartsEligibleForOfferWithSameSize)
                    ->where('carts.is_deleted',"N")
                    ->where('orders.id',$order_id)
                    ->select('carts.id','products.slug','products.quantity','carts.quantity','product_configuration.image','carts.product_id','carts.configuration_id','carts.quantity as cartQuantity','products.name','products.image as mainImage','product_configuration.price as mainPrice','products.discount_type','products.price','products.discount_price','products.is_active','products.in_sale','product_configuration.AttributeSize as sizeId','product_configuration.AttributeColor as colorId','product_configuration.price as configPrice','product_configuration.quantity as configQuantity')
                    ->get();

                $CartPriceData = OrderController::getCartPriceArray($cartData);
                /*if all products applicable for offer*/
                if($totalApplicableCartQuantity % $offer->quantity == 0){//if cart qty and offer items are in multiple
                    $totalDiscount = (array_sum($CartPriceData)*$offer->discount)/100;
                    $finalDiscount = $finalDiscount + $totalDiscount;//calculate total discount
                    $subtotalAfterDiscount = $subtotalAfterDiscount + (array_sum($CartPriceData) - $totalDiscount);
                    $totalShippingCharge = $totalShippingCharge +(($offer['shipping']*array_sum($CartPriceData))/100);
                }else{//if any product is in offer, but quantity exceed's offer limit then
                    $CartsDoesNotHaveOffers = ($totalApplicableCartQuantity % $offer->quantity);
                    $totalOfferApplicableProducts = $totalApplicableCartQuantity-$CartsDoesNotHaveOffers;
                    $CartPriceData = array_sort($CartPriceData);//sorting array price wise
                    $offerApplicableProducts = array_slice($CartPriceData, 0, $totalOfferApplicableProducts);
                    $offerNotApplicableProducts = array_slice($CartPriceData, $totalOfferApplicableProducts, count($CartPriceData)+1);
                    $totalDiscount = (array_sum($offerApplicableProducts)*$offer->discount)/100;
                    $finalDiscount = $finalDiscount + $totalDiscount;//calculate total discount
                    $subtotalAfterDiscount = $subtotalAfterDiscount + (array_sum($offerApplicableProducts)-$totalDiscount);
                    //shipping charge for applicable  offers product
                    $totalShippingCharge = $totalShippingCharge +(($offer['shipping']*array_sum($offerApplicableProducts))/100);
                    //if some products in carts does not contains offer.
                    $offerNotApplicableProductsSum = array_sum($offerNotApplicableProducts);
                    $subtotalAfterDiscount = $subtotalAfterDiscount + $offerNotApplicableProductsSum;
                    $totalShippingCharge = $totalShippingCharge + (ENV("WITHOUT_OFFER_ITEM_SHIPPING_CHARGE_IN_SAME_ORDER") * count($offerNotApplicableProducts) );
                }
                //add the products with offer but not eligible as diffrent size
                $cartData = Order::join('carts','carts.order_id','=','orders.id')
                    ->join('products','products.id','=','carts.product_id')
                    ->leftjoin('product_configuration','product_configuration.id','=','carts.configuration_id')
                    ->whereIn('carts.id',$cartsNotEligibleForOfferWithSameSize)
                    ->where('carts.is_deleted',"N")
                    ->where('orders.id',$order_id)
                    ->select('carts.id','products.slug','products.quantity','carts.quantity','product_configuration.image','carts.product_id','carts.configuration_id','carts.quantity as cartQuantity','products.name','products.image as mainImage','product_configuration.price as mainPrice','products.discount_type','products.price','products.discount_price','products.is_active','products.in_sale','product_configuration.AttributeSize as sizeId','product_configuration.AttributeColor as colorId','product_configuration.price as configPrice','product_configuration.quantity as configQuantity')
                    ->get();
                $CartPriceData = OrderController::getCartPriceArray($cartData);
                $subtotalAfterDiscount = $subtotalAfterDiscount + array_sum($CartPriceData);
                $totalShippingCharge = $totalShippingCharge + (ENV("WITHOUT_OFFER_ITEM_SHIPPING_CHARGE_IN_SAME_ORDER") * $cartData->sum("quantity") );
            }
            /*adding subtotal of other carts which does not have offer*/
            $arrayDiff = array_diff($allProductsIds,$allProductContainsOfferFinalArray);
            $cart_ids = Cart::where("order_id",$order->id)
                ->where('carts.is_deleted',"N")
                ->whereIn('product_id',$arrayDiff)
                ->pluck("id")->toArray();
            $cartData = Order::join('carts','carts.order_id','=','orders.id')
                ->join('products','products.id','=','carts.product_id')
                ->leftjoin('product_configuration','product_configuration.id','=','carts.configuration_id')
                ->whereIn('carts.id',$cart_ids)
                ->where('carts.is_deleted',"N")
                ->where('orders.id',$order_id)
                ->select('carts.id','products.slug','products.quantity','carts.quantity','product_configuration.image','carts.product_id','carts.configuration_id','carts.quantity as cartQuantity','products.name','products.image as mainImage','product_configuration.price as mainPrice','products.discount_type','products.price','products.discount_price','products.is_active','products.in_sale','product_configuration.AttributeSize as sizeId','product_configuration.AttributeColor as colorId','product_configuration.price as configPrice','product_configuration.quantity as configQuantity')
                ->get();
            $totalShippingCharge = $totalShippingCharge + (ENV("WITHOUT_OFFER_ITEM_SHIPPING_CHARGE_IN_SAME_ORDER") * $cartData->sum("quantity") );
            $otherThanOfferProductsSubTotal = OrderController::getSubtotalFromCartData($cartData);
            $data ['finalDiscount'] = $finalDiscount;
            $data['finalDiscountedAmount'] = $subtotalAfterDiscount+$otherThanOfferProductsSubTotal;
            $data['totalShippingCharge'] = $totalShippingCharge;
            $data['offerFlag'] = $offerFlag;
            return $data;
        }

    }

    public function orderList() {
        try {
            $user = Auth::user();
            $orderData = null;
            $OrderStatus = NULL;
            $orders = Order::where("customer_id", $user->id)->where("is_completed", "Y")->where("sub_total","!=",0)->orderBy("created_at", "DESC")->paginate(5);
            foreach ($orders as $key => $order) {
                $carts = Cart::where("order_id", $order['id'])->where("is_deleted", "N")->get();
                if ($carts) {
                    $orders[$key]['status'] = StatusMaster::where("status_id", $order['order_status'])->pluck("status");
                    $orders[$key]['note'] = $this->getOrderNote($order['order_status']);
                    $orders[$key]['cart'] = $carts;
                    $orders[$key]['userShownOrderId'] = $this->getOrderId($order['id']);
                    $orders[$key]['trackURL'] ="https://sportsdrive.shiprocket.co/tracking/".$order->tracking_number;
                } else {//if no cart is mapped to that order
                    unset($orders[$key]);
                }
            }
            $failedStatus = StatusMaster::where("slug", "failed")->pluck("status_id");
            $deliveredStatus = StatusMaster::where("slug", "order_delivered")->pluck("status_id");
            $shippedStatus = StatusMaster::where("slug", "order_shipped")->pluck("status_id");
            $cancelledStatus = StatusMaster::where("slug", "cancelled")->pluck("status_id");
            return view("user.my-order")->with(compact("orders", "deliveredStatus", "OrderStatus", "cancelledStatus","failedStatus","shippedStatus"));
        } catch (\Exception $e) {
            $data = [
                'input_params' => Auth::user(),
                'action' => 'my orders list',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function orderDetail($order_id) {
        try {
            $order_id = base64_decode($order_id);
            $user = Auth::user();
            $reviews = null;
            $order = Order::where("customer_id", $user->id)->where("id", $order_id)->where("is_completed", "Y")->first();
            if (!$order)
                abort(404);
            $carts = Cart::where("order_id", $order_id)->where("is_deleted", "N")->get();
            $order['status'] = StatusMaster::where("status_id", $order['order_status'])->pluck("status");
            $order['note'] = $this->getOrderNote($order['order_status']);
            $order['cart'] = $carts;
            $order['userShownOrderId'] = $this->getOrderId($order['id']);
            $allReviews = RatingReviews::where('email', $user->email_address)->get();
            if ($allReviews != null) {
                foreach ($allReviews as $key => $review) {
                    $reviews[$review->product_id] = intval($review->rating);
                }
            }
            $userShownOrderId =  NULL;
            $trackingResponse =  null;
            if($order['shiprocket_shipment_id'] != NULL){
                $token = $this->createToken();
                $trackingResponse = $this->trackOrder($order['shiprocket_shipment_id'],$token);
                $trackingResponse = (array) $trackingResponse->tracking_data;
            }
            $deliveredStatus = StatusMaster::where("slug", "delivered")->pluck("status_id");
            return view("user.order-details")->with(compact("order", "userShownOrderId", "user", "carts", "reviews", "deliveredStatus","trackingResponse"));
        } catch (\Exception $e) {
            $data = [
                'input_params' => $order_id,
                'action' => 'my orders Detail',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function failed(Request $request, $order_id) {
        try{
            $order = null;
            $order_id = base64_decode($order_id);
            $order = Order::where("id", $order_id)->first();
            if ($order == null)
                return redirect("/cart/view");
            $userShownOrderId = $this->getOrderId($order->id);
            return view("user.order-failed")->with(compact("order","userShownOrderId"));
        } catch (\Exception $e) {
            $data = [
                'input_params' => $order_id,
                'action' => ' orders failed',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public static function getCartPriceArray($cartData){
        $cartPriceArray = array();
        foreach ($cartData as $cart){
            $cartQuantity = $cart['cartQuantity'];
            if($cart->configPrice!= null)
                $finalPrice = $cart->configPrice-$cart->discount_price;
            else
                $finalPrice = $cart->price-$cart->discount_price;
            for($i=0;$i<$cartQuantity;$i++){
                $cartPriceArray[] =  $finalPrice;
            }
        }
        return $cartPriceArray;
    }

    public static function coupon($request, $flag = 0){
        $user = Auth::user();
        $data = $request->all();
        $additionalDiscount = 0;
        $finalDiscountAmount = 0;
        $status = 403;
        $data['order_id'] = base64_decode($data['order_id']);
        //check coupon is valid or not
        $coupon = coupons::where("code",$data['code'])->first();
        if($coupon){//check if coupons exists
            //check the same offer availed by user previously.
            $isOfferAvailedPreviously = 0;
            $couponUsers = CouponsUsers::where("coupon_id",$coupon['id'])->where("user_id",$user['id'])->first();
            if($couponUsers){
                $failedOrderStatus = StatusMaster::where("slug","failed")->where("is_order","Y")->first();
                $failedPaymentStatus = StatusMaster::where("slug","failed")->where("is_payment","Y")->first();
                $orders = Order::where("id",$couponUsers['order_id'])->where("order_status","!=",$failedOrderStatus->id)->where("order_status","!=",$failedPaymentStatus->id)->first();
                $isOfferAvailedPreviously = $orders;
            }
            if(array_key_exists("from_order_response",$data) && $data['from_order_response'] == 1){
                $isOfferAvailedPreviously = 0;
            }
            if($isOfferAvailedPreviously && $flag == 0){//if coupon used by user previously
                $orderId = OrderController::getOrderIdCart($couponUsers['order_id']);
                $message = "Sorry! You already used this coupon (".$data['code'].") on ".date("d-m-Y",strtotime($couponUsers['created_at']))." for order number ".$orderId;
            }else{//id coupon not used previously
                if($coupon['is_active']=="Y"){//if coupon is active
                    $productsHaveOffer = CouponsProducts::where("coupon_id",$coupon['id'])->pluck("product_id")->toArray();
                    $cart_ids = Cart::where("order_id",$data['order_id'])->whereIn("product_id",$productsHaveOffer)->pluck("id");
                    $cartData = Order::join('carts','carts.order_id','=','orders.id')->join('products','products.id','=','carts.product_id')->leftjoin('product_configuration','product_configuration.id','=','carts.configuration_id')->whereIn('carts.id',$cart_ids)->where('carts.is_deleted',"N")->select('carts.id','products.slug','products.quantity','carts.quantity','product_configuration.image','carts.product_id','carts.configuration_id','carts.quantity as cartQuantity','products.name','products.image as mainImage','product_configuration.price as mainPrice','products.discount_type','products.price','products.discount_price','products.is_active','products.in_sale','product_configuration.AttributeSize as sizeId','product_configuration.AttributeColor as colorId','product_configuration.price as configPrice','product_configuration.quantity as configQuantity')->get();
                    $subtotal = OrderController::getSubtotalFromCartDataFromMRP($cartData);
                    $additionalDiscount = round((($subtotal*$coupon['discount'])/100));
                    $offersPrices = OrderController::getOffersProductPrice();
                    $finalDiscountAmount = ($offersPrices['finalDiscountedAmount']-$additionalDiscount)+$offersPrices['totalShippingCharge'];
                    $status = 200;
                    $message = "Coupon (".$data['code'].") applied successfully.";
                }else{//if coupon is not active or expired
                    $message = "Sorry ! The coupon(".$data['code'].") is expired.";
                }
            }
        }else if(count(PromotionsCouponsUsers::where("code",$data['code'])->first())>0){
            $promotionCoupon = PromotionsCouponsUsers::where("code",$data['code'])->first();
            $coupon = coupons::where("id",$promotionCoupon->coupon_id)->first();
            if($promotionCoupon->email_address == $user->email_address  && $promotionCoupon->mobile_number == $user->phone){
                if($promotionCoupon->is_used == "Y" && $flag == 0){
                    $message = "Sorry ! The coupon(".$data['code'].") is already used.";
                }else if(strtotime(Carbon::now())<strtotime($promotionCoupon->valid_from) || strtotime(Carbon::now())>strtotime($promotionCoupon->valid_till) || $coupon->is_active == "N"){
                    $message = "Sorry ! The coupon(".$data['code'].") is expired OR Invalid.";
                }else{
                    $cart_ids = Cart::where("order_id",$data['order_id'])->pluck("id");
                    $cartData = Order::join('carts','carts.order_id','=','orders.id')->join('products','products.id','=','carts.product_id')->leftjoin('product_configuration','product_configuration.id','=','carts.configuration_id')->whereIn('carts.id',$cart_ids)->where('carts.is_deleted',"N")->select('carts.id','products.slug','products.quantity','carts.quantity','product_configuration.image','carts.product_id','carts.configuration_id','carts.quantity as cartQuantity','products.name','products.image as mainImage','product_configuration.price as mainPrice','products.discount_type','products.price','products.discount_price','products.is_active','products.in_sale','product_configuration.AttributeSize as sizeId','product_configuration.AttributeColor as colorId','product_configuration.price as configPrice','product_configuration.quantity as configQuantity')->get();
                    $subtotal = OrderController::getSubtotalFromCartDataFromMRP($cartData);
                    $additionalDiscount = round((($subtotal*$promotionCoupon['discount'])/100));
                    $offersPrices = OrderController::getOffersProductPrice();
                    $finalDiscountAmount = ($offersPrices['finalDiscountedAmount']-$additionalDiscount)+$offersPrices['totalShippingCharge'];
                    $status = 200;
                    $message = "Coupon (".$data['code'].") applied successfully.";
                }
            }else{
                $message = "Sorry ! The coupon(".$data['code'].") is not valid for you. Please enter the coupon which is received to you only.";
            }
        }else{//if coupon not exists
            $message = "Sorry ! The coupon(".$data['code'].") is not valid.";
        }
        $data = [
            "additionalDiscount" => $additionalDiscount,
            "finalDiscountAmount" => $finalDiscountAmount,
            'message' => $message,
            'status' => $status
        ];
        return $data;
    }
    public static function getOrderIdCart($orderId){
        $orderIdLength = strlen((string)$orderId);
        $requiredOrderIdLength = ENV('ORDERIDLENGHT1');
        $totalZeroesRequired = $requiredOrderIdLength-$orderIdLength;
        $string = null;
        for ($i = 0;$i<$totalZeroesRequired;$i++)
            $string= $string."0";
        $userShownOrderId = "OD".$string.$orderId;
        return $userShownOrderId;
    }

    public static function getSubtotalFromCartData($cartData){
        $totalFinalPrice = 0;
        foreach ($cartData as $cart){
            if($cart->configPrice!= null)
                $finalPrice = $cart->configPrice-$cart->discount_price;
            else
                $finalPrice = $cart->price-$cart->discount_price;
            $totalFinalPrice = $totalFinalPrice + ( $finalPrice * $cart->cartQuantity );
        }
        return $totalFinalPrice;
    }
    public static function getSubtotalFromCartDataFromMRP($cartData){
        $totalFinalPrice = 0;
        foreach ($cartData as $cart){
            if($cart->configPrice!= null)
                $finalPrice = $cart->configPrice;
            else
                $finalPrice = $cart->price;
            $totalFinalPrice = $totalFinalPrice + ( $finalPrice * $cart->cartQuantity );
        }
        return $totalFinalPrice;
    }

    public static function getOffersProductPrice(){
        $user = Auth::user();
        if($user!=null)
            $order = Order::where("customer_id",$user->id)->where("is_completed","N")->first();
        else{
            $order_id = session()->get("order_id");
            $order = Order::where("id",$order_id)->first();
        }
        if($order!=null){
            $allProductContainsOfferFinalArray = array();
            $allProductsIds = Cart::where("order_id",$order->id)
                ->where('is_deleted',"N")
                ->pluck("product_id")->toArray();
            //get all offers related to cart
            $offers = ProductsOffers::join("offers","products_offers.offer_id","=","offers.id")->where("offers.is_active","Y")->whereIn("product_id",$allProductsIds)->groupBy("products_offers.offer_id")->select("products_offers.*")->get();
            $subtotalAfterDiscount = 0;
            $finalDiscount = 0;
            $totalDiscount = 0;
            $totalShippingCharge = 0;
            $offerFlag = 0;
            /*this is loop for products which contains offers*/
            foreach ($offers as $key=>$offer){
                $offerFlag = 1;
                $allProductContainsOffer = ProductsOffers::whereIn("product_id",$allProductsIds)
                    ->where("offer_id","=",$offer['offer_id'])
                    ->pluck("product_id")->toArray();
                $allProductContainsOfferFinalArray = array_merge($allProductContainsOfferFinalArray,$allProductContainsOffer);
                $offer = Offers::where("id",$offer['offer_id'])->first();
                $cartsContainsThisParticularOffer = Cart::where("order_id",$order->id)
                    ->where('is_deleted',"N")
                    ->whereIn('product_id',$allProductContainsOffer)->get();
                $config_ids = array_column($cartsContainsThisParticularOffer->toArray(), 'configuration_id');
                $configurationsEligibleForOfferWithSameSize = array();
                foreach($config_ids as $ids){
                    $AttributeSize = ProductConfiguration::where("id",$ids)->pluck("AttributeSize");
                    $sameConfigurations = ProductConfiguration::whereIn("id",$config_ids)->where("AttributeSize",$AttributeSize[0])->pluck("id")->toArray();
                    $offerApplicableCartsItems = Cart::whereIn("configuration_id",$sameConfigurations)->where("order_id",$order->id)->where("is_deleted","N")->sum("quantity");
                    if($offerApplicableCartsItems>1){
                        $carts = Cart::whereIn("configuration_id",$sameConfigurations)->where("order_id",$order->id)->where("is_deleted","N")->pluck("id")->toArray();
                        $configurationsEligibleForOfferWithSameSize = array_unique(array_merge($configurationsEligibleForOfferWithSameSize,$carts));
                    }
                }
                $cartsEligibleForOfferWithSameSize = Cart::where("order_id",$order->id)
                    ->where('is_deleted',"N")
                    ->whereIn('id',$configurationsEligibleForOfferWithSameSize)
                    ->whereIn('product_id',$allProductContainsOffer)
                    ->pluck("id")->toArray();
                $cartsNotEligibleForOfferWithSameSize = array_diff(array_column($cartsContainsThisParticularOffer->toArray(),"id"),$cartsEligibleForOfferWithSameSize);
                $totalApplicableCartQuantity = Cart::whereIn("id",$cartsEligibleForOfferWithSameSize)->sum("quantity");
                $cartData = Order::join('carts','carts.order_id','=','orders.id')
                    ->join('products','products.id','=','carts.product_id')
                    ->leftjoin('product_configuration','product_configuration.id','=','carts.configuration_id')
                    ->whereIn('carts.id',$cartsEligibleForOfferWithSameSize)
                    ->where('carts.is_deleted',"N")
                    ->where('orders.is_completed',"N")
                    ->select('carts.id','products.slug','products.quantity','carts.quantity','product_configuration.image','carts.product_id','carts.configuration_id','carts.quantity as cartQuantity','products.name','products.image as mainImage','product_configuration.price as mainPrice','products.discount_type','products.price','products.discount_price','products.is_active','products.in_sale','product_configuration.AttributeSize as sizeId','product_configuration.AttributeColor as colorId','product_configuration.price as configPrice','product_configuration.quantity as configQuantity')
                    ->get();

                $CartPriceData = OrderController::getCartPriceArray($cartData);
                /*if all products applicable for offer*/
                if($totalApplicableCartQuantity % $offer->quantity == 0){//if cart qty and offer items are in multiple
                    $totalDiscount = (array_sum($CartPriceData)*$offer->discount)/100;
                    $finalDiscount = $finalDiscount + $totalDiscount;//calculate total discount
                    $subtotalAfterDiscount = $subtotalAfterDiscount + (array_sum($CartPriceData) - $totalDiscount);
                    $totalShippingCharge = $totalShippingCharge +(($offer['shipping']*array_sum($CartPriceData))/100);
                }else{//if any product is in offer, but quantity exceed's offer limit then
                    $CartsDoesNotHaveOffers = ($totalApplicableCartQuantity % $offer->quantity);
                    $totalOfferApplicableProducts = $totalApplicableCartQuantity-$CartsDoesNotHaveOffers;
                    $CartPriceData = array_sort($CartPriceData);//sorting array price wise
                    $offerApplicableProducts = array_slice($CartPriceData, 0, $totalOfferApplicableProducts);
                    $offerNotApplicableProducts = array_slice($CartPriceData, $totalOfferApplicableProducts, count($CartPriceData)+1);
                    $totalDiscount = (array_sum($offerApplicableProducts)*$offer->discount)/100;
                    $finalDiscount = $finalDiscount + $totalDiscount;//calculate total discount
                    $subtotalAfterDiscount = $subtotalAfterDiscount + (array_sum($offerApplicableProducts)-$totalDiscount);
                    //shipping charge for applicable  offers product
                    $totalShippingCharge = $totalShippingCharge +(($offer['shipping']*array_sum($offerApplicableProducts))/100);
                    //if some products in carts does not contains offer.
                    $offerNotApplicableProductsSum = array_sum($offerNotApplicableProducts);
                    $subtotalAfterDiscount = $subtotalAfterDiscount + $offerNotApplicableProductsSum;
                    $totalShippingCharge = $totalShippingCharge + (ENV("WITHOUT_OFFER_ITEM_SHIPPING_CHARGE_IN_SAME_ORDER") * count($offerNotApplicableProducts) );
                }
                //add the products with offer but not eligible as diffrent size
                $cartData = Order::join('carts','carts.order_id','=','orders.id')
                    ->join('products','products.id','=','carts.product_id')
                    ->leftjoin('product_configuration','product_configuration.id','=','carts.configuration_id')
                    ->whereIn('carts.id',$cartsNotEligibleForOfferWithSameSize)
                    ->where('carts.is_deleted',"N")
                    ->where('orders.is_completed',"N")
                    ->select('carts.id','products.slug','products.quantity','carts.quantity','product_configuration.image','carts.product_id','carts.configuration_id','carts.quantity as cartQuantity','products.name','products.image as mainImage','product_configuration.price as mainPrice','products.discount_type','products.price','products.discount_price','products.is_active','products.in_sale','product_configuration.AttributeSize as sizeId','product_configuration.AttributeColor as colorId','product_configuration.price as configPrice','product_configuration.quantity as configQuantity')
                    ->get();
                $CartPriceData = OrderController::getCartPriceArray($cartData);
                $subtotalAfterDiscount = $subtotalAfterDiscount + array_sum($CartPriceData);
                $totalShippingCharge = $totalShippingCharge + (ENV("WITHOUT_OFFER_ITEM_SHIPPING_CHARGE_IN_SAME_ORDER") * $cartData->sum("quantity") );
            }
            /*adding subtotal of other carts which does not have offer*/
            $arrayDiff = array_diff($allProductsIds,$allProductContainsOfferFinalArray);
            $cart_ids = Cart::where("order_id",$order->id)
                ->where('carts.is_deleted',"N")
                ->whereIn('product_id',$arrayDiff)
                ->pluck("id")->toArray();
            $cartData = Order::join('carts','carts.order_id','=','orders.id')
                ->join('products','products.id','=','carts.product_id')
                ->leftjoin('product_configuration','product_configuration.id','=','carts.configuration_id')
                ->whereIn('carts.id',$cart_ids)
                ->where('carts.is_deleted',"N")
                ->where('orders.is_completed',"N")
                ->select('carts.id','products.slug','products.quantity','carts.quantity','product_configuration.image','carts.product_id','carts.configuration_id','carts.quantity as cartQuantity','products.name','products.image as mainImage','product_configuration.price as mainPrice','products.discount_type','products.price','products.discount_price','products.is_active','products.in_sale','product_configuration.AttributeSize as sizeId','product_configuration.AttributeColor as colorId','product_configuration.price as configPrice','product_configuration.quantity as configQuantity')
                ->get();
            $totalShippingCharge = $totalShippingCharge + (ENV("WITHOUT_OFFER_ITEM_SHIPPING_CHARGE_IN_SAME_ORDER") * $cartData->sum("quantity") );
            $otherThanOfferProductsSubTotal = OrderController::getSubtotalFromCartData($cartData);
            $data ['finalDiscount'] = $finalDiscount;
            $data['finalDiscountedAmount'] = $subtotalAfterDiscount+$otherThanOfferProductsSubTotal;
            $data['totalShippingCharge'] = $totalShippingCharge;
            $data['offerFlag'] = $offerFlag;
            return $data;
        }

    }
}




/* public function getcancelData($order_id) {
        try {
            $user = Auth::user();
            $order = Order::where("customer_id", $user->id)->where("id", $order_id)->where("is_completed", "Y")->first()->toArray();
            if (!$order)
                abort(404);
            $carts = Cart::where("order_id", $order_id)->where("is_deleted", "N")->get();
            $order['status'] = StatusMaster::where("status_id", $order['order_status'])->pluck("status");
            $order['cart'] = $carts;
            $deliveredStatus = StatusMaster::where("slug", "delivered")->pluck("status_id");
            $cancelorderData = null;
            foreach ($order['cart'] as $cart) {
                $cancelorderData .= "<tr>";
                $cancelorderData .= "<td>";
                $cancelorderData .= "<div class='item-image-name'>";
                $cancelorderData .= "<div class='imgWr'><img src='images/product1.jpg' alt='image'></div>";
                $cancelorderData .= "<div class='item-name'><a href='/product/details/{{$cart->product_slug}}'>$cart->product_name</a><div class='other-info'><p>Color:$cart->color</p><p>Size:$cart->size</p></div></div>";
                $cancelorderData .= "<td><span>1</span></td>";
                $cancelorderData .= "<td><span>â‚¹$cart->final_price</span></td>";
                $cancelorderData .= "</div>";
                $cancelorderData .= "</td>";
                $cancelorderData .= "</tr>";
            }
            $finalData = [
                'cancelorderData' => $cancelorderData,
            ];
            return $finalData;
            return view("user.my-order");
        } catch (\Exception $e) {
            $data = [
                'input_params' => $order_id,
                'action' => 'Cancel Order',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function cancelOrder(Request $request) {
        try {
            $data = $request->except('_token');
            $order_id = $data['order_id'];
            $insert = OrderReturn::create($data);
            $user = Auth::user();
            $cancelledStatus = StatusMaster::where("slug", "cancelled")->pluck("status_id");
            Order::where('customer_id', '=', $user->id)->where("id", $order_id)->update(array('order_status' => $cancelledStatus[0],
                'updated_at' => Carbon::now()));

            $orders = Order::findorfail($order_id);
            $shipping_address_details = json_decode($orders->shipping_address);
            $billing_address_details = json_decode($orders->billing_address);
            $carts = Cart::where("order_id", $order_id)->where("is_deleted", "N")->get();

            Mail::send('user.emails.order_cancel_email', ['order' => $orders, 'cart' => $carts, 'shipping_address_details' => $shipping_address_details, 'billing_address_details' => $billing_address_details], function ($m) use ($data, $user) {
                $m->subject('Sports Drive | Your order is cancel');
                $m->from(ENV('ADMIN_ORDER_EMAIL_ID'), 'Sports Drive');
                $m->to($user['email_address'])->subject('Sports Drive | Your order is cancel');
                $m->cc(ENV('ADMIN_ORDER_EMAIL_ID'), 'Sports Drive')->subject('Sports Drive | Your order is cancel');
            });


            $message = 'Your details Successfully Updated.';
            return redirect('/order/list')->with('success', $message);
        } catch (\Exception $e) {
            $data = [
                'input_params' => NULL,
                'action' => 'My Cancel Order',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function getreturnData($order_id) {
        try {
            $user = Auth::user();
            $order = Order::where("customer_id", $user->id)->where("id", $order_id)->where("is_completed", "Y")->first()->toArray();
            if (!$order)
                abort(404);
            $carts = Cart::where("order_id", $order_id)->where("is_deleted", "N")->get();
            $order['status'] = StatusMaster::where("status_id", $order['order_status'])->pluck("status");
            $order['cart'] = $carts;
            $deliveredStatus = StatusMaster::where("slug", "delivered")->pluck("status_id");
            $returnorderData = null;
            foreach ($order['cart'] as $cart) {
                $returnorderData .= "<tr>";
                $returnorderData .= "<td>";
                $returnorderData .= "<div class='item-image-name'>";
                $returnorderData .= "<div class='imgWr'><img src='images/product1.jpg' alt='image'></div>";
                $returnorderData .= "<div class='item-name'><a href='/product/details/{{$cart->product_slug}}'>$cart->product_name</a><div class='other-info'><p>Color:$cart->color</p><p>Size:$cart->size</p></div></div>";
                $returnorderData .= "<td><span>1</span></td>";
                $returnorderData .= "<td><span>â‚¹$cart->final_price</span></td>";
                $returnorderData .= "</div>";
                $returnorderData .= "</td>";
                $returnorderData .= "</tr>";
            }
            $finalData = [
                'returnorderData' => $returnorderData,
            ];
            return $finalData;
            return view("user.my-order");
        } catch (\Exception $e) {
            $data = [
                'input_params' => $order_id,
                'action' => 'Return Order',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function returnOrder(Request $request) {
        try {
            $data = $request->except('_token');
            $data["is_return"] = "Y";
            $order_id = $data['order_id1'];
            $insert = OrderReturn::create($data);
            $user = Auth::user();
            $returncancelledStatus = StatusMaster::where("slug", "return_cancelled")->pluck("status_id");
            Order::where('customer_id', '=', $user->id)->where("id", $order_id)->update(array('order_status' => $returncancelledStatus[0],
                'updated_at' => Carbon::now()));

            $orders = Order::findorfail($order_id);
            $carts = Cart::where("order_id", $order_id)->where("is_deleted", "N")->get();
            $shipping_address_details = json_decode($orders->shipping_address);
            $billing_address_details = json_decode($orders->billing_address);

            Mail::send('user.emails.order_return_email', ['order' => $orders, 'cart' => $carts, 'shipping_address_details' => $shipping_address_details, 'billing_address_details' => $billing_address_details], function ($m) use ($data, $user) {
                $m->subject('Sports Drive | Your order is return');
                $m->from(ENV('ADMIN_ORDER_EMAIL_ID'), 'Sports Drive');
                $m->to($user['email_address'])->subject('Sports Drive | Your order is return');
                $m->cc(ENV('ADMIN_ORDER_EMAIL_ID'), 'Sports Drive')->subject('Sports Drive | Your order is return');
            });

            $message = 'Your details Successfully Updated.';
            return redirect('/order/list')->with('success', $message);
        } catch (\Exception $e) {
            $data = [
                'input_params' => NULL,
                'action' => 'My Cancel Order',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }*/
?>