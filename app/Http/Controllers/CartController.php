<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Http\Controllers\CustomTraits\CartTrait;
use App\Http\Controllers\CustomTraits\VisitorsTrait;
use App\Order;
use App\Product;
use App\ProductConfiguration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    //traits
    use CartTrait;
    use VisitorsTrait;
    public function __construct(){
        $this->middleware('checkVisitors');
    }
    /**
     * @param Request $request
     * @param $product_id
     * @return \Illuminate\Http\RedirectResponse
     * add to cart as user as well as guest
     */
    public function addToCart(Request $request,$product_id){
        try{
            $errorMessageArray = array();
            $data = $request->all();
            $product = Product::where("id", $product_id)->first();
            $configData = null;
            $configurationId = null;
            $productQty = $product->quantity;

            if ( array_key_exists("selectedColor", $data) 
                && $data['selectedColor'] !== null 
                && array_key_exists("selectedSize", $data) 
                && $data['selectedSize'] !== null) {// if color and size both selected

                $configData = ProductConfiguration::where('product_id', $product_id)
                    ->where('AttributeColor', $data['selectedColor'])
                    ->where('AttributeSize', $data['selectedSize'])
                    ->first();
                if ($configData === null)
                    return redirect()->back()->with('error', "Selected Configuration is Not Available.Please try Again.");
                $configurationId = $configData->id;
                $productQty = $configData->quantity;
            } elseif (array_key_exists("selectedColor", $data) 
                && $data['selectedColor'] !== null ) { // if only color is selected
                $configData = ProductConfiguration::where('product_id', $product_id)
                    ->where('AttributeColor', $data['selectedColor'])
                    ->where('AttributeSize', NULL)
                    ->first();
                if($configData === null)
                    return redirect()->back()->with('error', "Selected Configuration is Not Available.Please try Again.");
                $configurationId = $configData->id;
                $productQty = $configData->quantity;
            } elseif (array_key_exists("selectedSize", $data) 
                && $data['selectedSize'] !== null ) {// if only size is selected
                $configData = ProductConfiguration::where('product_id', $product_id)
                    ->where('AttributeColor', NULL)
                    ->where('AttributeSize', $data['selectedSize'])
                    ->first();
                if ($configData === null)
                    return redirect()->back()->with('error', "Selected Configuration is Not Available.Please try Again.");
                $configurationId = $configData->id;
                $productQty = $configData->quantity;
            }

            $user = Auth::user();
            $orderId = null;
            $orderData = null;
            $cart = null;
            $order = null;
            $eventData = (object) [];
            $errorMessageArray = array();

            if ( $user )
                $eventData = $user;
            else {
                $eventData->id = "";
                $eventData->first_name = "Visitor";
                $eventData->last_name = "";
            }
            $eventData->productName = $product->name;
            if ( $user !== null ) {
                /*code added on 28-12-2017 to check if there is multiple order which is not completed start*/
                $orders = Order::where("customer_id", $user->id)->where("is_completed", "N")->pluck("id");
                $totalIncompleteOrder = count($orders);
                if ($totalIncompleteOrder > 0 ) {
                    $orderId = $orders[ $totalIncompleteOrder - 1 ];
                    if($totalIncompleteOrder > 1 ){
                        //unset last order from array and make other completed
                        unset($orders[$totalIncompleteOrder - 1]);
                        Order::whereIn("id", $orders->toArray())
                            ->update(
                                array(
                                    "is_completed" => "Y",
                                    "is_payment_proceed" => "Y",
                                    "order_status" => 11,
                                    "payment_status" => 11,
                                    "is_abandoned" => "Y"
                                )
                            );
                    }
                    $order = Order::where('id', $orderId)->first();
                }
                /*code added on 28-12-2017 to check if there is multiple order which is not completed end*/
            } else {
                $order_id = session()->get("order_id");
                if ($order_id !== null) {
                    $order = Order::where('id',$order_id)->first();
                }
            }

            $requestedQty = $data['quantity'];
            if ($order !== null) {//if configured product and there is already unplaced order
                $cart = Cart::where('order_id', $order->id)
                    ->where('product_id', $product_id)
                    ->where('configuration_id', $configurationId)
                    ->where('is_deleted', 'N')
                    ->first();
            }

            if ($cart !== null)
                $requestedQty = $requestedQty + $cart->quantity;
            if ($productQty < $requestedQty) {
                $message = "Uh Oh! Looks like we don't have the quantity you requested. We have ".$productQty." though.";
                //trigger the event
                $this->StoreNotificationData($eventData, "add_cart_not_available");
                return redirect()->back()->with("error", $message);
            }

            if ($order === null) {//there is no order found
                $orderData = [
                    'invoice_id' => null,
                    'customer_id' => $user['id'],
                    'order_date' => Carbon::now(),
                    'total_cart_item' => $data['quantity'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'is_completed' => 'N',
                ];
                $order = Order::create($orderData);
            } else {
                $orderData = [
                    'total_cart_item' => $order->total_cart_item + $data['quantity'],
                ];
                Order::where('id', $order->id)->update($orderData);
            }

            // echo json_encode($cart); exit();

            if ( !isset($cart) || count($cart) <= 0) {
                $cartData = [
                    'order_id'      => $order->id,
                    'product_id'    => $product_id,
                    'configuration_id'=> $configurationId,
                    'quantity'      => $data['quantity'],
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ];
                Cart::create($cartData);
                //trigger the event
                $this->StoreNotificationData($eventData, "add_cart");
                $message = 'product has been successfully added to your cart.';
            } else {
                $cartData = [
                    'quantity'  => $cart->quantity + $data['quantity'],
                    'updated_at'=> Carbon::now(),
                ];
                Cart::where('id', $cart->id)->update($cartData);
                //trigger the event
                $this->StoreNotificationData($eventData, "update_cart");
                $message = 'Cart updated successfully.';
            }

            //for other products, which is in offer, and qty selected
            if (array_key_exists("otherQuantity", $data)) {
                $totalQty = 0;
                if (!array_key_exists("selectedSize", $data))
                    $data['selectedSize'] = NULL;
                if (!array_key_exists("AttributeColor", $data))
                    $data['AttributeColor'] = NULL;
                foreach ($data['otherQuantity'] as $key => $otherProductQty ) {
                    if ($otherProductQty['quantity'] > 0 ) {
                        $configData = ProductConfiguration::where('product_id', $key)
                            ->where('AttributeSize', $data['selectedSize'])
                            ->first();
                        if ($configData !==null 
                                && $configData->quantity >= $otherProductQty['quantity']) {
                            $configurationId = $configData->id;
                            $cart = Cart::where('order_id', $order->id)
                                ->where('product_id', $key)
                                ->where('configuration_id', $configurationId)
                                ->where('is_deleted', 'N')
                                ->first();
                            if (count($cart) <= 0 ) {
                                $cartData = [
                                    'order_id'      => $order->id,
                                    'product_id'    => $key,
                                    'configuration_id'=> $configurationId,
                                    'quantity'      => $otherProductQty['quantity'],
                                    'created_at'    => Carbon::now(),
                                    'updated_at'    => Carbon::now(),
                                ];
                                Cart::create($cartData);
                            } else {
                                $cartData = [
                                    'quantity' => $cart->quantity + $otherProductQty['quantity'],
                                    'updated_at'=>Carbon::now(),
                                ];
                                Cart::where('id', $cart->id)->update($cartData);
                            }
                            $totalQty = $totalQty + $otherProductQty['quantity'];
                        } else {
                            $productName = Product::where("id", $key)->pluck("name");
                            if ($configData === null)
                                $errorMessageArray[] = "Sorry ! The requested size for $productName[0] is not available with us.";
                            if ($configData->quantity < $otherProductQty['quantity'])
                                $errorMessageArray[] = "Sorry ! The requested quantity for $productName[0] is not available with us. We have $configData->quantity though.";
                        }
                        //update total cart item
                        $orderData = [
                            'total_cart_item' => $order->total_cart_item + $totalQty
                        ];
                        Order::where('id', $order->id)->update($orderData);
                    }
                }
            }
            if ($user === null) {
                session()->put("order_id", $order->id);
            }
            session()->put("errorArray", $errorMessageArray);
            /*get cart data and redirect to cart*/
            return redirect("/cart/view");
        }catch(\Exception $e){
            $data = [
                'input_params' => $data,
                'action' => 'addToCart',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    /**
     * @return $this
     * View cart as user as well as guest
     */
    public function viewCart(){
        try {
            $user = Auth::user();
            $cartData = null;
            // get all carts & data related this user
            if ($user != null)
                $cartData = $this->getUserCart();
            else {
                $order_id = session()->get("order_id");
                $cart_ids = Cart::where("order_id", $order_id)->pluck("id");
                $cartData = $this->getGuestCart($cart_ids);
            }
            if ($cartData != null) {
                $cartData = $this->getAttributeDataFromCartData($cartData);
            }
            // echo json_encode($cartData); exit();
            $offersPrices = $this->getOffersProductPrice();
            $errorMessageArray = array();
            $shipping_charge = 100;
            return view("user.cart-view")
                ->with(
                    compact(
                        'cartData',
                        'shipping_charge',
                        "errorMessageArray",
                        "offersPrices"
                    )
                );
        } catch (\Exception $e){
            $data = [
                'input_params' => Auth::user(),
                'action' => 'viewCart',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    /**
     * @param Request $request
     * @return array
     * Update Cart Item on ajax call
     */
    public function update(Request $request){
        try{
            $data = $request->all();
            $cart = Cart::where('id', $data['cart_id'])->first();
            $product = product::where('id', $cart['product_id'])->first();
            $order = Order::where('id', $cart['order_id'])->first();
            if ($cart['configuration_id'] !== null) {
                $configurationInfo = ProductConfiguration::where('id', $cart['configuration_id'])->first();
                $availableQty = $configurationInfo->quantity;
                $price = $configurationInfo->price;
            } else {
                $availableQty = $product->quantity;
                $price = $product->price;
            }
            if ($cart['is_deleted'] === "Y" || $order->is_completed === "Y" ) {//cart is deleted or order is placed , returning unauthorised response.
                $data['message'] ="Sorry ! Something went wrong!Please try again.";
                $data['quantity'] = $cart['quantity'];
                $data['status'] = 406;
                return $data;
            }
            if($availableQty >= $data['qty']){
                if ($cart['quantity'] == $data['qty']){
                    $data['message'] = "Sorry !The quantity that you are requesting, is already added to cart.";
                    $data['quantity'] = $cart['quantity'];
                    $data['status'] = 401;
                    return $data;
                } else {
                    $cartData = [
                        'quantity' => $data['qty'],
                        'updated_at' => Carbon::now(),
                    ];
                    Cart::where('id', $data['cart_id'])->update($cartData);
                    $orderData = [
                        'total_cart_item' => $order->total_cart_item + $data['qty'],
                        'updated_at'=>Carbon::now(),
                    ];
                    Order::where('id', $order->id)->update($orderData);
                }
                $cart = Cart::where('id', $data['cart_id'])->first();
                $offersPrices = $this->getOffersProductPrice();
                $data['originalPricePerProduct'] = number_format($price, 2);
                $data['finalPricePerProduct'] = number_format($price-$product->discount_price, 2);
                $data['cart_total'] = number_format(($price-$product->discount_price)*$cart->quantity, 2);
                $data['final_total'] = number_format($offersPrices['finalDiscountedAmount'] + $offersPrices['finalDiscount'], 2);
                $data['final_discount'] = number_format($offersPrices['finalDiscount'], 2);
                $data['estimated_total'] = number_format($offersPrices['finalDiscountedAmount'], 2);
                $data['status'] = 200;
                $data['quantity'] = $cart['quantity'];
                $data['message'] ="Cart Updated successfully.";
                $data['shipping_charge'] = 100;
                return $data;
            }else{
                $data['status'] = 403;
                $data['quantity'] = $cart['quantity'];
                $data['message'] = "<strong>Uh Oh!</strong> Looks like we don't have the quantity you requested."."". " We have"." ".$availableQty." "."though";
                return $data;
            }

        }catch(\Exception $e){
            $data = [
                'input_params' => Auth::user(),
                'action' => 'viewCart',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    /**
     * @param Request $request
     * @return array|int|string
     * remove item from cart
     */
    public function remove(Request $request){
        try{
            $data = $request->all();
            $cart = Cart::where('id', $data['cart_id'])->first();
            $order = Order::where('id', $cart->order_id)->first();
            if ($cart->is_deleted == "Y" || $order->is_completed == "Y")
                return "403";//cart is deleted or order is placed , returning unauthorised response.
            if ( !empty($cart) ) {
                Cart::where('id',$data['cart_id'])->update(array('is_deleted'=>'Y', 'updated_at'=>Carbon::now()));
                $orderData = [
                    'total_cart_item' => $order->total_cart_item - $cart->quantity,
                    'updated_at' => Carbon::now(),
                ];
                Order::where('id', $order->id)->update($orderData);
                $order = Order::where('id', $order->id)->first();
                $allCartIds = Cart::where('order_id', $order->id)->where("is_deleted", "N")->pluck("id");
                $cartData = $this->getGuestCart($allCartIds);
                $offersPrices = $this->getOffersProductPrice();
                $data['final_total'] = number_format($offersPrices['finalDiscountedAmount'] + $offersPrices['finalDiscount'], 2);
                $data['final_discount'] = number_format($offersPrices['finalDiscount'], 2);
                $data['estimated_total'] = number_format($offersPrices['finalDiscountedAmount'], 2);
                $data['total_cart_count'] = count($cartData);
                return $data;
            }else{
                return 0;
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => Auth::user(),
                'action' => 'viewCart',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
}
