<?php 
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\CustomTraits\CartTrait;
use App\Address;
use App\Http\Controllers\CustomTraits\LoginTraits;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomTraits\OrderTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Validator;
use DB;
use App\Admin;
use Carbon\Carbon;
use App\Product;
use App\Order;
use App\Customer;
use App\StatusMaster;
use App\Cart;
class OrdersController extends Controller
{
    use OrderTrait;
    use LoginTraits;
    use CartTrait;
    public function __construct(){
       $this->middleware('adminauth');
    }

    public function listOrders(Request $request ,$custId=null) {
        try{ 
            if($custId != null){
                $orders = Order::where('customer_id',$custId)->where('is_completed','Y')->orderby("updated_at", "DESC")->get();
                $customer = Customer::where('id',$custId)->first();

            }else{
                $orders = Order::select('orders.*','customers.first_name','customers.last_name')->where('is_completed','Y')->join('customers','customers.id','=','orders.customer_id')->orderby("updated_at", "DESC")->get();
                $customer = null;
            }
            $requiredOrderIdLength = ENV("ORDERIDLENGHT");
            $userShownOrderId = array();
            $customerData = array();
            $orderStatus = array();
            $paymentStatus = array();
            foreach ($orders as $key => $order) {
                $userShownOrderId[$order->id] = $this->getOrderId($order->id);
                $orderStatus[$order->id] = StatusMaster::where('status_id',$order->order_status)->first();
                $paymentStatus[$order->id] =StatusMaster::where('status_id',$order->payment_status)->first();
            }
             $allPaymentStatus = StatusMaster::where('is_payment', 'Y')->get();
             $allOrderStatus = StatusMaster::where('is_order', 'Y')->get();
             $byOrderStatus = "0";
             $byPaymentStatus = "0";
            return view('admin.list-orders')->with(compact('orders','userShownOrderId','orderStatus','paymentStatus','allPaymentStatus','allOrderStatus','byOrderStatus','byPaymentStatus','customer'));
        }catch(\Exception $e){  
            $data = [
                'input_params' => $request,
                'action' => 'Admin list Orders',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function listOrdersFilter(Request $request, $custId=null) {
        try{ 
            $data = $request->all();
              $customer = null;
            $allPaymentStatus = StatusMaster::where('is_payment', 'Y')->pluck("status_id");
            $allOrderStatus = StatusMaster::where('is_order', 'Y')->pluck("status_id");
            $byOrderStatus = $data['byOrderStatus'];
            $byPaymentStatus = $data['byPaymentStatus'];
            if($data['byOrderStatus']!=0){
                $byOrderStatusArray = array( $data['byOrderStatus']);
            }else{
                $byOrderStatusArray = $allOrderStatus;
            }
            if($data['byPaymentStatus']!=0){
                $byPaymentStatusArray = array( $data['byPaymentStatus']);
            }else{
                $byPaymentStatusArray = $allPaymentStatus;
            }
            $dateRage = explode('-', $data['dateRange']);
            $startDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $dateRage[0])));
            $endDate = date("Y-m-d 23:59:59", strtotime(str_replace('/', '-', $dateRage[1])));
            if($custId != null){
                 $orders = Order::where('customer_id',$custId)->where('is_completed','Y')->whereIn('orders.order_status',$byOrderStatusArray)->whereIn('orders.payment_status',$byPaymentStatusArray)->whereBetween('orders.order_date', array($startDate, $endDate)) ->orderBy('created_at','DESC')->get();
                $customer = Customer::where('id',$custId)->first();
            }else{
                $orders = Order::join('customers','customers.id','=','orders.customer_id')->where('is_completed','Y')->whereIn('orders.order_status',$byOrderStatusArray)->whereIn('orders.payment_status',$byPaymentStatusArray)->whereBetween('orders.order_date', array($startDate, $endDate)) ->orderBy('created_at','DESC')->select('orders.*','customers.first_name','customers.last_name')->get();
            }
            $userShownOrderId = array();
            $customerData = array();
            $orderStatus = array();
            $paymentStatus = array();
            foreach ($orders as $key => $order) {
                $userShownOrderId[$order->id] = $this->getOrderId($order->id);
                $orderStatus[$order->id] = StatusMaster::where('status_id',$order->order_status)->first();
                $paymentStatus[$order->id] =StatusMaster::where('status_id',$order->payment_status)->first();
            }
            $allPaymentStatus = StatusMaster::where('is_payment', 'Y')->get();
            $allOrderStatus = StatusMaster::where('is_order', 'Y')->get();
            return view('admin.list-orders')->with(compact('orders','userShownOrderId','orderStatus','paymentStatus','allPaymentStatus','allOrderStatus','byOrderStatus','byPaymentStatus','customer'));
        }catch(\Exception $e){ 
            $data = [
                'input_params' => $request,
                'action' => 'Admin list Orders filter',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function viewOrders(Request $request ,$orderId) {
        try{ 
            $data = Order::select('orders.*','customers.first_name','customers.last_name')->where('orders.id',$orderId)->join('customers','customers.id','=','orders.customer_id')->orderBy('created_at','DESC')->first();
            $userShownOrderId = null;
            $userShownOrderId =$this->getOrderId($orderId);
            //current order status name
            $data->order_status_name = StatusMaster::where('status_id',$data->order_status)->where('is_order', 'Y')->first();
            //next order status
            if ($data->order_status_name->slug == "delivered" || $data->order_status_name->slug == "order_refund" || $data->order_status_name->slug == "order_delivered" )//delivered and cancelled for previous status and order_delivered for new status
                $nextStatus= null;
            else if($data->order_status_name->slug == "pending"){//for pending status
                $nextStatus = StatusMaster::where('slug', 'order_packed')->first();
            }else if($data->order_status_name->slug == "cancelled"){
                $nextStatus = StatusMaster::where('slug', 'order_refund')->first();
            }else {
                $nextStatus = StatusMaster::take(1)->skip($data->order_status-1)->first();
            }
            /*if ($data->order_status == 3 || $data->order_status == 4 )
                $nextStatus= null;
            else
                $nextStatus = StatusMaster::where('is_order', 'Y')->take(1)->skip($data->order_status)->first();*/
            //current paymeny status
            $data->payment_status_name = StatusMaster::where('status_id',$data->payment_status)->where('is_payment', 'Y')->first();
            //next payment status
            if ($data->payment_status == 9 || $data->payment_status == 11 )
                $allPaymentStatus= null;
            else
                $allPaymentStatus = StatusMaster::where('is_payment', 'Y')->get();
            $carts = Cart::select('carts.*','products.name','products.image')->where("order_id",$orderId)->where("is_deleted","N")->join('products','products.id','=','carts.product_id')->get();
            $allReturnStatus = StatusMaster::where('is_return', 'Y')->get();
            $readyToShipStatus = StatusMaster::where('slug', 'order_packed')->first();
            $shippedStatus = StatusMaster::where('slug', 'order_shipped')->first();
            $deliveredStatus = StatusMaster::where('slug', 'order_delivered')->first();
            $cancelledStatus = StatusMaster::where('slug', 'cancelled')->first();
            $orderRefundStatus = StatusMaster::where('slug', 'order_refund')->first();
            return view('admin.view-order')->with(compact('data','userShownOrderId','carts','allReturnStatus','allPaymentStatus','nextStatus','shippedStatus','deliveredStatus','cancelledStatus','orderRefundStatus','readyToShipStatus'));
        }catch(\Exception $e){ 
            $data = [
                'input_params' => $request,
                'action' => 'Admin list Orders',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function changeStatus(Request $request){
        try{
            $data = $request->all();
            $checkOrder = Order::find($data['order_id']);
            if($data['ready_to_ship_status_id'] == $data['order_status'] && $checkOrder['is_pushed_to_shiprocket'] != "Y"){
                $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                    'length' => 'required|numeric|between:0.5,99.99',
                    'breadth' => 'required|numeric|between:0.5,99.99',
                    'height' => 'required|numeric|between:0.5,99.99',
                    'weight' =>'required|numeric|between:0.1,99.99',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }
            if($checkOrder->order_status == $data['order_status'] ){//if same status then redirect back
                return redirect('/administrator/view-Orders/'.$data['order_id'])->with('error','Status Not Changed Successfully. Please try again.');
            }
            $arrData = [
                'payment_status' => $data['payment_status'],
                'order_status' => $data['order_status'],
                'updated_at' => Carbon::now(),
            ];
            $status = StatusMaster::where('slug', 'order_delivered')->first();
            if($data['order_status'] == $status->id){
              $arrData['delivery_date'] = Carbon::now();
            }
            if(array_key_exists("courier_text_box",$data)){
                $arrData['tracking_number'] =  $data['courier_text_box'];
            }
            if(array_key_exists("delivered_to",$data)){
                $arrData['delivered_to'] =  $data['delivered_to'];
            }
            if(array_key_exists("new_delivery_date",$data)){
                $arrData['new_delivery_date'] =  $data['new_delivery_date'];
            }
            if(array_key_exists("cancellation_reason",$data)){
                $arrData['cancellation_reason'] =  $data['cancellation_reason'];
            }
            if(array_key_exists("refund_amount",$data)){
                $arrData['refund_amount'] =  $data['refund_amount'];
            }
            if(array_key_exists("refund_bank_id",$data)){
                $arrData['refund_bank_id'] =  $data['refund_bank_id'];
            }
            if(array_key_exists("refund_bank_ref",$data)){
                $arrData['refund_bank_ref'] =  $data['refund_bank_ref'];
            }

            if(array_key_exists("length",$data)){
                $arrData['length'] =  $data['length'];
            }
            if(array_key_exists("breadth",$data)){
                $arrData['breadth'] =  $data['breadth'];
            }
            if(array_key_exists("height",$data)){
                $arrData['height'] =  $data['height'];
            }
            if(array_key_exists("weight",$data)){
                $arrData['weight'] =  $data['weight'];
            }
            if($data['ready_to_ship_status_id'] == $data['order_status'] && $checkOrder['is_pushed_to_shiprocket'] != "Y"){
                $returnData = $this->sendOrderToShipRocket($data,$checkOrder);
                if ($returnData['status'] == 500) {
                    if (isset($returnData['data']['errors'])) {
                        return redirect('/administrator/view-Orders/'.$data['order_id'])->withErrors($returnData['data']['errors']);
                    } elseif(isset($returnData['data']['message'])) {
                        return redirect('/administrator/view-Orders/'.$data['order_id'])->with("error",$returnData['data']['message']);
                    }elseif(isset($returnData['data']['status'])){
                        return redirect('/administrator/view-Orders/'.$data['order_id'])->withErrors($returnData['data']['status']);
                    }else {
                        return redirect('/administrator/view-Orders/'.$data['order_id'])->with("error","Something went wrong. Please contact site administrators");
                    }
                }
            }
            $updateVal = Order::where('id',$data['order_id'])->update($arrData);
            $order = Order::select('orders.*','customers.first_name','customers.last_name','customers.email_address')->where('orders.id',$data['order_id'])->join('customers','customers.id','=','orders.customer_id')->orderBy('created_at','DESC')->first();
            $siteDetails = Admin::select('facebook_url','twitter_url','googleplus_url','instagram_url')->first();
            $carts = Cart::where("order_id",$data['order_id'])->where("is_deleted", "N")->get();
            foreach ($carts as $key=>$cart){
                $carts[$key]['sku'] = Product::where("id",$cart->product_id)->pluck("sku")->toArray();
            }
            $paymentStatusName_prev = StatusMaster::where('status_id',$data['payment_status_prev'])->where('is_payment', 'Y')->first();
            $orderStatusName_prev = StatusMaster::where('status_id', $data['order_status_prev'])->where('is_order', 'Y')->first();
            $userShownOrderId = $this->getOrderId($data['order_id']);
            $paymentStatusName = StatusMaster::where('status_id',$data['payment_status'])->where('is_payment', 'Y')->first();
            $orderStatusName = StatusMaster::where('status_id', $data['order_status'])->where('is_order', 'Y')->first();
            $order_id = $data['order_id'];
            /*send sms code starts*/
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
            if ($orderStatusName->slug == "order_packed") {
                $message = rawurlencode("Order Packed & Ready to Ship: Your www.SportsDrive.in ORDER #$userShownOrderId for Rs.$amount has been packed and is ready to ship.");
            }elseif ($orderStatusName->slug == "order_shipped"){
                $trackingURL =  "https://sportsdrive.shiprocket.co/tracking/".$order['tracking_number'];
                $message = rawurlencode("Order Shipped: Your www.SportsDrive.in ORDER #$userShownOrderId for Rs.$amount has been shipped. You may track your order here $trackingURL");
            }elseif ($orderStatusName->slug == "order_delivered"){
                $deliveredTo = $order['delivered_to'];
                $deliveryDate = $order['new_delivery_date'];
                $message = rawurlencode("Order Delivered: Your www.SportsDrive.in ORDER #$userShownOrderId for Rs.$amount has been delivered to $deliveredTo on $deliveryDate");
            }elseif ($orderStatusName->slug == "cancelled"){
                $cancellationReason = $data['cancellation_reason'];
                $message = rawurlencode("Order Cancelled: Your www.SportsDrive.in ORDER #$userShownOrderId for Rs. $amount has been cancelled. $cancellationReason");
            }elseif ($orderStatusName->slug == "order_refund"){
                $refund_amount = $data['refund_amount'];
                $refund_bank_id = $data['refund_bank_id'];
                $refund_bank_ref = $data['refund_bank_ref'];
                $message = rawurlencode("Payment Refund: Amount of Rs.$refund_amount against your www.SportsDrive.in ORDER #$userShownOrderId for Rs.$amount has been refunded. ID #$refund_bank_id & Ref #$refund_bank_ref");
            }
            $numbers = implode(',', $numbers);
            // Prepare data for POST request
            $orderData = $data;
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
            $data = $orderData ;
            /*send mail code starts*/
            $emailMessage = rawurldecode($message);
            Mail::send('admin.emails.order-status-change-mail-user', ['order' => $data['order_id'], 'cart' => $carts, 'total_cart_item' =>$order['total_cart_item'], 'orders' =>$order, 'payment_status_db' =>$paymentStatusName_prev['status'], 'order_status_db' =>$orderStatusName_prev['status'], 'user' => $order['first_name'], 'orderStatus' => $orderStatusName['status'], 'paymentStatus' => $paymentStatusName['status'], 'siteDetails' => $siteDetails,'orderNo' =>$userShownOrderId,'emailMessage' =>$emailMessage], function($message) use ($order,$userShownOrderId) {
                $message->from(ENV('ADMIN_ORDER_EMAIL_ID'),'SportsDrive.In');
                $message->to($order['email_address'])->subject('SportsDrive.In | Order Status Changed '." #".$userShownOrderId);
                $message->cc(ENV('ADMIN_ORDER_EMAIL_ID'),'SportsDrive.In')->subject('SportsDrive.In | Order Status Changed '." #".$userShownOrderId);
            });

            /*send mail code ends*/
            if($updateVal > 0){
                return redirect('/administrator/view-Orders/'.$order_id)->with('success','Status Changed Successfully.');
            }else{
                return redirect('/administrator/view-Orders/'.$order_id)->with('error','Status Not Changed Successfully. Please try again.');
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request,
                'action' => 'changeStatus Orders',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function sendOrderToShipRocket($data,$checkOrder){
        $carts = Cart::select('carts.*','products.name','products.image',"products.sku","products.gst","products.hsn")->where("order_id",$data['order_id'])->where("is_deleted","N")->join('products','products.id','=','carts.product_id')->get();
        $orderDetails = array();
        $cartWiseShippingCharges = $this->getCartWiseShippingCharges($checkOrder,$carts);
        foreach ($carts as $cart){
            !empty($cart->size) ? $skuAppend = $cart->size: $skuAppend =$cart->configuration_id;
            $orderDetails[] = [
                "name"=> $cart->product_name,
                "sku"=> $cart->sku."-".$skuAppend,
                "units"=> $cart->quantity,
                "selling_price" => ( $cart->price_per_qty - $cart->discount_per_qty ) + $cartWiseShippingCharges[$cart->id]['shipping'] - $cartWiseShippingCharges[$cart->id]['discount'],
                "discount"=> 0,
                "tax"=> $cart->gst,
                "hsn"=>  $cart->hsn,
            ];
        }
        $shipping_address = json_decode($checkOrder->shipping_address);
        $billing_address = json_decode( $checkOrder->billing_address);
        $orderDetails = [
            "order_id"=> "SD-".$this->getOrderId($checkOrder->id),
            "order_date"=> $checkOrder->order_date,
            "pickup_location"=> "Primary",
            "channel_id"=> "Custom",
            "comment"=> "Reseller : SportsDrive.in - SPORTIFF INDIA PVT LTD",
            "reseller_name"=> "SportsDrive.in - SPORTIFF INDIA PVT LTD",
            "company_name"=> "",
            "billing_customer_name"=> $checkOrder->customer->first_name,
            "billing_last_name"=> $checkOrder->customer->last_name,
            "billing_address"=> ucfirst($billing_address->address_line_1),
            "billing_address_2"=> ucfirst($billing_address->address_line_2),
            "billing_isd_code"=> "",
            "billing_city"=> $billing_address->city,
            "billing_pincode"=>  $billing_address->pin_code,
            "billing_state"=>  $billing_address->state,
            "billing_country"=>  $billing_address->country,
            "billing_email"=> $checkOrder->customer['email_address'],
            "billing_phone"=> trim($checkOrder->customer->phone),
            "billing_alternate_phone"=>trim($billing_address->contact_no),
            "shipping_is_billing"=> false,
            "shipping_customer_name"=>  $checkOrder->customer->first_name,
            "shipping_last_name"=>$checkOrder->customer->last_name,
            "shipping_address"=>  ucfirst($shipping_address->address_line_1),
            "shipping_address_2"=>  ucfirst($shipping_address->address_line_2),
            "shipping_city"=> $shipping_address->city,
            "shipping_pincode"=> $shipping_address->pin_code,
            "shipping_country"=> $shipping_address->country,
            "shipping_state"=> $shipping_address->state,
            "shipping_email"=> $checkOrder->customer['email_address'],
            "shipping_phone"=> trim($checkOrder->customer->phone),
            "order_items"=> $orderDetails,
            "payment_method"=> "Prepaid",
            "shipping_charges"=> "0",
            "giftwrap_charges"=> "0",
            "transaction_charges"=> "0",
            "total_discount"=> "0",
            "sub_total"=> $checkOrder->total,
            "length"=> $data['length'],
            "breadth"=> $data['breadth'],
            "height"=> $data['height'],
            "weight"=> $data['weight'],
            "ewaybill_no"=> "",
            "customer_gstin"=> "",
            "invoice_number"=>"",
            "order_type"=>"ESSENTIALS",
        ];
        $token = $this->createToken();
        $response = $this->createOrder($orderDetails,$token);
        $response= (array) $response;
        if($response['status_code'] == 1){
            $shipRocketData['shiprocket_order_id'] = $response['order_id'];
            $shipRocketData['shiprocket_shipment_id'] = $response['shipment_id'];
            Order::where("id",$data['order_id'])->update($shipRocketData);
            return ['status' => 200, 'data' => $response];
        }else{
            return ['status' => 500, 'data' => $response];
        }
    }
    public function generateInvoice($orderID) {
        try{
            $invoiceID =$this->getUniqueID();
            $arrData =[
                    'invoice_id'=>$invoiceID,
                    'updated_at'=>Carbon::now(),
            ];
            $updateVal = Order::where('id',$orderID)->update($arrData);
            if($updateVal >0){
                    return redirect('/administrator/view-Orders/'.$orderID)->with('success','Invoice generated Successfully.');
            }else{
                    return redirect('/administrator/view-Orders/'.$orderID)->with('error','Invoice NOT generated Successfully. Please try again.');
            }
        }catch(\Exception $e){ dd($e->getMessage());
            $data = [
                'input_params' => $orderID,
                'action' => 'generateInvoice',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public static function getUniqueID() {
        $IDLEN = 5;
        $transOrdID = null;
        $date = strtotime(date("Y-m-d H:i:s"));
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
        $transOrdID =  $tmpordID . $date;

        return $transOrdID;
    }

    public function updateOrderShippingAddress($order_id){
        $order = Order::find($order_id);
        $addresses = json_decode($order->shipping_address);
        $addressType = "shipping";
        return view("admin.update-order-address")->with(compact("addresses","order_id","addressType"));
    }

    public function updateOrderBillingAddress($order_id){
        $order = Order::find($order_id);
        $addresses = json_decode($order->billing_address);
        $addressType = "billing";
        return view("admin.update-order-address")->with(compact("addresses","order_id","addressType"));
    }

    public function updateCustomerAddress(Request $request,$order_id){
        $order = Order::find($order_id);
        $data = $request->except("_token");
        if($data['address_type'] == "billing"){
            $order->billing_address = json_encode(array_merge(json_decode($order->billing_address,true),$data));
        }else{
            $order->shipping_address = json_encode(array_merge(json_decode($order->shipping_address,true),$data));
        }
        if (isset($data['update_to_customer_master']) && $data['update_to_customer_master'] == "on"){
            $address = Address::where("id",$data['id'])->first();
            $address->update($data);
        }
        $order->save();
        return redirect('/administrator/view-Orders/'.$order_id)->with('success','Address updates successfully.');
    }
}
