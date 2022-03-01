<?php

namespace App\Http\Controllers;

use App\Address;
use App\Http\Controllers\CustomTraits\CartTrait;
use App\Http\Controllers\CustomTraits\VisitorsTrait;
use App\Order;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests;

class CheckoutController extends Controller
{
    /**
     * CheckoutController constructor.
     */
    public function __construct(){
        $this->middleware('userauth');
        $this->middleware('checkVisitors');
    }
    use CartTrait;
    use VisitorsTrait;
    /**
     * @return $this
     * Checkout step 1 view to display address, shipping data , payment method etc.
     */
    public function checkoutStep1(){
        try{
            $errorMessageArray = null;
            $user = Auth::user();
            $cartData = $this->getUserCart();
            /*check the product is_active,is_completed or not #start modified on 20/08/2018*/
            $errorArray = [];
            foreach ($cartData as $cart){
                $product = Product::find($cart->product_id);
                if($product->is_active != "Y" || $product->is_completed != "Y" ){
                    $errorArray[] = "$product->name is not available with us, Please remove from cart to proceed with checkout.";
                }
                if(count($errorArray)>0){
                    return redirect()->back()->with(compact("errorArray"));
                }
            }
            /*check the product is_active,is_completed or not #end modified on 20/08/2018*/
            if( count($cartData) <= 0)//if there is no any order
                return redirect("/cart/view");
            else {
                foreach ($cartData as $cartItem) {
                    if ($cartItem->configuration_id!=null){
                            if($cartItem->configQuantity<$cartItem->cartQuantity)
                             $errorMessageArray[$cartItem->id] = "Uh Oh! Looks like we don't have the quantity you requested for ".$cartItem->name.". We have ".$cartItem->configQuantity." though.";
                    } else {
                        if ($cartItem->quantity<$cartItem->cartQuantity) {
                            $errorMessageArray[$cartItem->id] = "Uh Oh! Looks like we don't have the quantity you requested for ".$cartItem->name.". We have ".$cartItem->quantity." though.";
                        }
                    }
                }
                if ($errorMessageArray != null ) // if qty is not available for any product which is added to cart.
                    return redirect("/cart/view")->with("errorArray",$errorMessageArray);
            }
            $subtotal = $this->getSubtotalFromCartData($cartData);
            $defaultShippingAddress = Address::where("customer_id",$user->id)->where("is_shipping","Y")->where("is_default","Y")->first();
            $defaultBillingAddress = Address::where("customer_id",$user->id)->where("is_billing","Y")->where("is_default","Y")->first();
            $allShippingAddresses = Address::where("customer_id",$user->id)->where("is_shipping","Y")->get();
            $allBillingAddresses = Address::where("customer_id",$user->id)->where("is_billing","Y")->get();
            $cartCount = count($cartData);
            $offersPrices = $this->getOffersProductPrice();
            $standardCharges = ENV("STANDARD_SHIPPING_CHARGES");
            $expressCharges = ENV("EXPRESS_SHIPPING_CHARGES");
            /*trigger notification #start*/
            $user->cartCount = $cartCount;
            $user->subTotal = $offersPrices['finalDiscountedAmount']+$offersPrices['totalShippingCharge'];
            $this->StoreNotificationData($user, "checkout");
            /*trigger notification #end*/
            return view("user.checkout-1")->with(
                compact(
                    "defaultShippingAddress",
                    "defaultBillingAddress",
                    "allShippingAddresses",
                    "allBillingAddresses",
                    "subtotal",
                    "cartCount",
                    "standardCharges",
                    "expressCharges",
                    "offersPrices"
                )
            );
        }catch(\Exception $e){
            $data = [
                'input_params' => Auth::user(),
                'action' => 'checkoutStep1',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    /**
     * @param Requests\CheckoutStepOne $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function checkoutStep2(Requests\CheckoutStepOne $request){
        try{
            $user = Auth::user();
            $data = $request->all();
            if(!(array_key_exists('billing_address_id',$data)) && array_key_exists("same",$data) && $data['same']=="Y")//if user clicked on same as shipping address
                $data['billing_address_id'] = $data['shipping_address_id'];
            if(!(array_key_exists('billing_address_id',$data))){
                $data['billing_address_id'] = $data['shipping_address_id'];
            }
            $cartData = $this->getUserCart();
            if(count($cartData)<=0)//if there is no any order
                return redirect("/cart/view");
            $subtotal = $this->getSubtotalFromCartData($cartData);
            $defaultShippingAddress = Address::where("id",$data['shipping_address_id'])->first();
            $defaultBillingAddress = Address::where("id",$data['billing_address_id'])->first();
            $offersPrices = $this->getOffersProductPrice();
            $standardCharges = ENV("STANDARD_SHIPPING_CHARGES");
            $expressCharges = ENV("EXPRESS_SHIPPING_CHARGES");
            $cartCount = count($cartData);
            $order = Order::where("customer_id",$user->id)->where("is_completed","N")->first();
            return view("user.review-order")->with(compact("subtotal","defaultShippingAddress","defaultBillingAddress","data","standardCharges","expressCharges","cartCount","order","offersPrices"));
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'checkoutStep2',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }


    public function addBillingAddress(Request $request){
        try{
            $data = [
                'is_billing' => "Y",
                'is_shipping' => "N",
                "route"=>"/checkout/1"
            ];

            return view("user.add-address")->with(compact("data"));
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'add Billing Address at checkout',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function addShippingAddress(Request $request){
        try{
            $data = [
                'is_shipping' => "Y",
                'is_billing' => "N",
                "route" => "/checkout/1"
            ];
            // return view("user.add-address")->with(compact("subtotal","defaultShippingAddress","defaultBillingAddress","data","standardCharges","expressCharges","cartCount","order"));
            return view("user.add-address")->with(compact('data'));
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'add shipping Address at checkout',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function editAddress(Request $request,$id){
        try{
            $data = [
                "route" => "/checkout/1"
            ];
            $address_display = Address::where('id',$id)->first();
            return view('user.edit-address')->with(compact("address_display",'data'));
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'add shipping Address at checkout',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

}
