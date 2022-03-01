<?php
namespace App\Http\Controllers\CustomTraits;


use App\Attribute;
use App\Cart;
use App\coupons;
use App\CouponsProducts;
use App\CouponsUsers;
use App\Offers;
use App\Order;
use App\ProductConfiguration;
use App\ProductsOffers;
use App\PromotionsCouponsUsers;
use App\StatusMaster;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait CartTrait{

    public function getUserCart(){
        $user = Auth::user();
        $orderId = Order::where("customer_id",$user->id)->where("is_completed","N")->pluck("id");
        $totalIncompleteOrder = count($orderId);
        $cartData = null;
        if($totalIncompleteOrder > 0){
            $lastOrder = $orderId[$totalIncompleteOrder-1];
            if($totalIncompleteOrder>1){
                //unset last order from array and make other completed
                unset($orderId[$totalIncompleteOrder-1]);
                Order::whereIn("id",$orderId->toArray())->update(array("is_completed"=>"Y","is_payment_proceed"=>"Y","order_status"=>11,"payment_status"=>11,"is_abandoned"=>"Y"));
            }
            $cartData = Order::join('carts','carts.order_id','=','orders.id')
                ->join('products','products.id','=','carts.product_id')
                ->leftjoin('product_configuration','product_configuration.id','=','carts.configuration_id')
                ->where('orders.customer_id',$user->id)
                ->where('orders.id',$lastOrder)
                ->where('carts.is_deleted',"N")
                ->where('orders.is_completed',"N")
                ->select('carts.id','products.slug','products.quantity','carts.quantity','product_configuration.image','carts.product_id','carts.configuration_id','carts.quantity as cartQuantity','products.name','products.image as mainImage','product_configuration.price as mainPrice','products.discount_type','products.price','products.discount_price','products.is_active','products.in_sale','product_configuration.AttributeSize as sizeId','product_configuration.AttributeColor as colorId','product_configuration.price as configPrice','product_configuration.quantity as configQuantity')
                ->get();
        }
        return $cartData;
    }

    public function getGuestCart($cart_ids){
        $cartData = Order::join('carts','carts.order_id','=','orders.id')
            ->join('products','products.id','=','carts.product_id')
            ->leftjoin('product_configuration','product_configuration.id','=','carts.configuration_id')
            ->whereIn('carts.id',$cart_ids)
            ->where('carts.is_deleted',"N")
            ->where('orders.is_completed',"N")
            ->select('carts.id','products.slug','products.quantity','carts.quantity','product_configuration.image','carts.product_id','carts.configuration_id','carts.quantity as cartQuantity','products.name','products.image as mainImage','product_configuration.price as mainPrice','products.discount_type','products.price','products.discount_price','products.is_active','products.in_sale','product_configuration.AttributeSize as sizeId','product_configuration.AttributeColor as colorId','product_configuration.price as configPrice','product_configuration.quantity as configQuantity')
            ->get();
        return $cartData;
    }

    public function getSubtotalFromCartData($cartData){
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

    public function getSubtotalFromCartDataFromMRP($cartData){
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

   public function getAttributeDataFromCartData($cartData){

        if (is_array($cartData) || is_object($cartData))
        {
           foreach ($cartData as $key=>$cart){
               $color = Attribute::where("id",$cart->colorId)->pluck("name");
               $size = Attribute::where("id",$cart->sizeId)->pluck("name");
               $cartData[$key]->color = $color;
               $cartData[$key]->size = $size;
           }
        }

       // foreach ($cartData as $key=>$cart){
       //     $color = Attribute::where("id",$cart->colorId)->pluck("name");
       //     $size = Attribute::where("id",$cart->sizeId)->pluck("name");
       //     $cartData[$key]->color = $color;
       //     $cartData[$key]->size = $size;
       // }
        return $cartData;
    }

    public function getOffersProductPrice(){
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
            $offers = ProductsOffers::join("offers","products_offers.offer_id","=","offers.id")
                ->where("offers.is_active","Y")
                ->whereIn("product_id",$allProductsIds)
                ->groupBy("products_offers.offer_id")
                ->select("products_offers.*")
                ->get();
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

                $CartPriceData = $this->getCartPriceArray($cartData);
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
                $CartPriceData = $this->getCartPriceArray($cartData);
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
            $otherThanOfferProductsSubTotal = $this->getSubtotalFromCartData($cartData);
            $data ['finalDiscount'] = $finalDiscount;
            $data['finalDiscountedAmount'] = $subtotalAfterDiscount+$otherThanOfferProductsSubTotal;
            $data['totalShippingCharge'] = $totalShippingCharge;
            $data['offerFlag'] = $offerFlag;
            return $data;
        }

   }


    public function getOffersProductPriceFromOrderId($order_id){
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

                $CartPriceData = $this->getCartPriceArray($cartData);
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
                $CartPriceData = $this->getCartPriceArray($cartData);
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
            $otherThanOfferProductsSubTotal = $this->getSubtotalFromCartData($cartData);
            $data ['finalDiscount'] = $finalDiscount;
            $data['finalDiscountedAmount'] = $subtotalAfterDiscount+$otherThanOfferProductsSubTotal;
            $data['totalShippingCharge'] = $totalShippingCharge;
            $data['offerFlag'] = $offerFlag;
            return $data;
        }

    }


   public function getCartPriceArray($cartData){
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



    public function coupon($request, $flag = 0){

        $user = Auth::user();
        $data = $request->all();
        $additionalDiscount = 0;
        $finalDiscountAmount = 0;
        $status = 403;
        $data['order_id'] = base64_decode($data['order_id']);

        //check coupon is valid or not
        $coupon = coupons::where("code",$data['code'])->first();

        if( isset($coupon) ){//check if coupons exists
            //check the same offer availed by user previously.
            $isOfferAvailedPreviously = 0;
            $couponUsers = CouponsUsers::where("coupon_id",$coupon['id'])->where("user_id",$user['id'])->first();
            if( isset($couponUsers) ){
                $failedOrderStatus = StatusMaster::where("slug","failed")->where("is_order","Y")->first();
                $failedPaymentStatus = StatusMaster::where("slug","failed")->where("is_payment","Y")->first();
                $orders = Order::where("id",$couponUsers['order_id'])->where("order_status","!=",$failedOrderStatus->id)->where("order_status","!=",$failedPaymentStatus->id)->first();
                $isOfferAvailedPreviously = $orders;
            }
            if(array_key_exists("from_order_response",$data) && $data['from_order_response'] == 1){
                $isOfferAvailedPreviously = 0;
            }
            if($isOfferAvailedPreviously && $flag == 0){//if coupon used by user previously
                $orderId = $this->getOrderIdCart($couponUsers['order_id']);
                $message = "Sorry! You already used this coupon (".$data['code'].") on ".date("d-m-Y",strtotime($couponUsers['created_at']))." for order number ".$orderId;
            }else{//id coupon not used previously
                if($coupon['is_active']=="Y"){//if coupon is active
                    $productsHaveOffer = CouponsProducts::where("coupon_id",$coupon['id'])->pluck("product_id")->toArray();
                    $cart_ids = Cart::where("order_id",$data['order_id'])->whereIn("product_id",$productsHaveOffer)->pluck("id");
                    $cartData = Order::join('carts','carts.order_id','=','orders.id')
                                        ->join('products','products.id','=','carts.product_id')
                                        ->leftjoin('product_configuration','product_configuration.id','=','carts.configuration_id')
                                        ->whereIn('carts.id',$cart_ids)
                                        ->where('carts.is_deleted',"N")
                                        ->select('carts.id','products.slug','products.quantity','carts.quantity','product_configuration.image','carts.product_id','carts.configuration_id','carts.quantity as cartQuantity','products.name','products.image as mainImage','product_configuration.price as mainPrice','products.discount_type','products.price','products.discount_price','products.is_active','products.in_sale','product_configuration.AttributeSize as sizeId','product_configuration.AttributeColor as colorId','product_configuration.price as configPrice','product_configuration.quantity as configQuantity')
                                        ->get();
                    $subtotal = $this->getSubtotalFromCartDataFromMRP($cartData);
                    $additionalDiscount = round((($subtotal*$coupon['discount'])/100));
                    $offersPrices = $this->getOffersProductPrice();
                    $finalDiscountAmount = ($offersPrices['finalDiscountedAmount']-$additionalDiscount)+$offersPrices['totalShippingCharge'];
                    $status = 200;
                    $message = "Coupon (".$data['code'].") applied successfully.";
                }else{//if coupon is not active or expired
                    $message = "Sorry ! The coupon(".$data['code'].") is expired.";
                }
            }
        }else if( PromotionsCouponsUsers::where("code",$data['code'])->first() !== null ){
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
                    $subtotal = $this->getSubtotalFromCartDataFromMRP($cartData);
                    $additionalDiscount = round((($subtotal*$promotionCoupon['discount'])/100));
                    $offersPrices = $this->getOffersProductPrice();
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
    public function getOrderIdCart($orderId){
        $orderIdLength = strlen((string)$orderId);
        $requiredOrderIdLength = ENV('ORDERIDLENGHT1');
        $totalZeroesRequired = $requiredOrderIdLength-$orderIdLength;
        $string = null;
        for ($i = 0;$i<$totalZeroesRequired;$i++)
            $string= $string."0";
        $userShownOrderId = "OD".$string.$orderId;
        return $userShownOrderId;
    }


    public function getCartPriceArrayShipRocket($cartData){
        $cartPriceArray = array();
        foreach ($cartData as $cart){
            for($i=0;$i<$cart->quantity;$i++){
                $cartPriceArray[$cart->id."-".$i] =  $cart->price_per_qty-$cart->discount_price;
            }
        }
        return $cartPriceArray;
    }

    public function getCartWiseShippingCharges($order,$carts){
        if($order != null){
            $cartWiseShippingCharge = array();
            $cartProductOffers = array();
            foreach ($carts as $cart){
                $cartWiseShippingCharge[$cart->id]["shipping"] = 0;
                $cartWiseShippingCharge[$cart->id]["discount"] = 0;
                $productOffer = ProductsOffers::join("offers","products_offers.offer_id","=","offers.id")->where("offers.is_active","Y")->where("product_id",$cart->product_id)->groupBy("products_offers.offer_id")->select("products_offers.*")->first();
                if(!empty($productOffer)){ // when product in offer
                    $offer = Offers::where("id",$productOffer['offer_id'])->where("offers.is_active","Y")->first();
                    !isset($cartProductOffers[$offer->id]["totalApplicableCartQuantity"]) ? $cartProductOffers[$offer->id]["totalApplicableCartQuantity"]= 0: "";
                    !isset($cartProductOffers[$offer->id]["cartFinalSum"]) ? $cartProductOffers[$offer->id]["cartFinalSum"]= 0 : "";
                    if($offer){//calculate carwise shipping charges
                        $cartProductOffers[$offer->id][] = $cart;
                        $cartProductOffers[$offer->id]["totalApplicableCartQuantity"] = $cartProductOffers[$offer->id]["totalApplicableCartQuantity"]+$cart->quantity;
                        $cartProductOffers[$offer->id]["cartFinalSum"] = $cartProductOffers[$offer->id]["cartFinalSum"]+$cart->final_price;
                    }else{
                        $cartWiseShippingCharge[$cart->id]["shipping"] = (ENV("WITHOUT_OFFER_ITEM_SHIPPING_CHARGE_IN_SAME_ORDER") * $cart->quantity );
                        $cartWiseShippingCharge[$cart->id]["discount"] = 0;
                    }
                }else{
                    $cartWiseShippingCharge[$cart->id]["shipping"] = (ENV("WITHOUT_OFFER_ITEM_SHIPPING_CHARGE_IN_SAME_ORDER") * $cart->quantity );
                    $cartWiseShippingCharge[$cart->id]["discount"] = 0;
                }
            }
            if(!empty($cartProductOffers)){
                foreach ($cartProductOffers as $offer_id => $cartProductOffer){
                    $offer = Offers::where("id",$offer_id)->first();
                    unset($cartProductOffer['cartFinalSum']);
                    unset($cartProductOffer['totalApplicableCartQuantity']);
                    $config_ids = array_column($cartProductOffer, 'configuration_id');
                    $cart_ids = array_column($cartProductOffer, 'id');
                    $allProductContainsOffer = array_column($cartProductOffer, 'product_id');
                    $cartsContainsThisParticularOffer = Cart::where("order_id",$order->id)->where('is_deleted',"N")->whereIn('product_id',$allProductContainsOffer)->get();
                    $configurationsEligibleForOfferWithSameSize = array();
                    foreach($config_ids as $ids){
                        $AttributeSize = ProductConfiguration::where("id",$ids)->pluck("AttributeSize");
                        $sameConfigurations = ProductConfiguration::whereIn("id",$config_ids)->where("AttributeSize",$AttributeSize[0])->pluck("id")->toArray();
                        $offerApplicableCartsItems = Cart::whereIn("configuration_id",$sameConfigurations)->whereIn("id",$cart_ids)->where("is_deleted","N")->sum("quantity");
                        if($offerApplicableCartsItems > 1){
                            $carts = Cart::whereIn("configuration_id",$sameConfigurations)->whereIn("id",$cart_ids)->where("is_deleted","N")->pluck("id")->toArray();
                            $configurationsEligibleForOfferWithSameSize = array_unique(array_merge($configurationsEligibleForOfferWithSameSize,$carts));
                        }
                    }
                    $cartsEligibleForOfferWithSameSize = Cart::where("order_id",$order->id)->where('is_deleted',"N")->whereIn('id',$configurationsEligibleForOfferWithSameSize)->whereIn('product_id',$allProductContainsOffer)->pluck("id")->toArray();
                    $cartsNotEligibleForOfferWithSameSize = array_diff(array_column($cartsContainsThisParticularOffer->toArray(),"id"),$cartsEligibleForOfferWithSameSize);
                    $cartData = Cart::whereIn("id",$cartsEligibleForOfferWithSameSize)->get();
                    $cartsNotEligibleForOfferWithSameSize = Cart::whereIn("id",$cartsNotEligibleForOfferWithSameSize)->get();
                    $totalApplicableCartQuantity = $cartData->sum("quantity");
                    $totalApplicableCartSum = $cartData->sum("final_price");
                    if($totalApplicableCartQuantity > 0){
                        if($totalApplicableCartQuantity % $offer->quantity == 0){//if cart qty and offer items are in multiple
                            $totalShippingCharge = ($offer['shipping'] * $totalApplicableCartSum / 100);
                            $eachCartShippingCharges = $totalShippingCharge/$totalApplicableCartQuantity;
                            foreach ($cartData as $cart){
                                $cartWiseShippingCharge[$cart->id]["shipping"] = $eachCartShippingCharges;
                                $cartWiseShippingCharge[$cart->id]["discount"] = (($cart->final_price*$offer->discount)/100)/$cart->quantity;
                            }
                        }else{//if any product is in offer, but quantity exceed's offer limit then
                            $CartsDoesNotHaveOffers = ($totalApplicableCartQuantity % $offer->quantity);
                            $totalOfferApplicableProducts = $totalApplicableCartQuantity-$CartsDoesNotHaveOffers;
                            $CartPriceData = $this->getCartPriceArrayShipRocket($cartData);
                            $CartPriceData = array_sort($CartPriceData);//sorting array price wise
                            $offerApplicableProducts = array_slice($CartPriceData, 0, $totalOfferApplicableProducts);
                            $offerNotApplicableProducts = array_slice($CartPriceData, $totalOfferApplicableProducts, count($CartPriceData)+1);
                            //shipping charge for applicable  offers product
                            $offerTotalShippingCharge = (($offer['shipping']*array_sum($offerApplicableProducts))/100);
                            //if some products in carts does not contains offer.
                            $eachCartShippingCharges = $offerTotalShippingCharge/$totalOfferApplicableProducts;
                            foreach ($offerApplicableProducts as $key=>$offerApplicableProduct) {
                                $cart_id = explode("-",$key)[0];
                                $cartWiseShippingCharge[$cart_id]["shipping"] =  $cartWiseShippingCharge[$cart_id]["shipping"] + $eachCartShippingCharges;
                                $cartWiseShippingCharge[$cart_id]["discount"] = $cartWiseShippingCharge[$cart_id]["discount"]+(($offerApplicableProduct*$offer->discount)/100);
                            }
                            foreach ($offerNotApplicableProducts as $key=>$offerNotApplicableProduct) {
                                $cart_id = explode("-",$key)[0];
                                $cartWiseShippingCharge[$cart_id]["shipping"] =  $cartWiseShippingCharge[$cart_id]["shipping"] + ENV("WITHOUT_OFFER_ITEM_SHIPPING_CHARGE_IN_SAME_ORDER");
                            }
                        }
                    }
                    if(!empty($cartsNotEligibleForOfferWithSameSize)){
                        foreach ($cartsNotEligibleForOfferWithSameSize as $cart){
                            $cartWiseShippingCharge[$cart->id]["shipping"] =(ENV("WITHOUT_OFFER_ITEM_SHIPPING_CHARGE_IN_SAME_ORDER") * $cart->sum("quantity") );
                            $cartWiseShippingCharge[$cart->id]["discount"] = 0;
                        }
                    }
                }
            }
            return $cartWiseShippingCharge;
        }
    }
}


