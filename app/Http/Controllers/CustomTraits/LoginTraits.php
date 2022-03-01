<?php
namespace App\Http\Controllers\CustomTraits;
use App\Cart;
use App\Order;
use Illuminate\Support\Facades\Auth;

trait LoginTraits{
        public function loginUser( $data ) {
            if ( Auth::attempt( [
                    'email_address' => $data['email_address'],
                    'password' => $data['password']
                    ] )
            ) {
                $order_id = session()->get("order_id");
                $order = Order::where('id',$order_id)->where('is_completed','N')->first();// fetching current order which is not complete and placed as geust
                $user = Auth::user();
                if($order !== null){
                    $previousCartIds = array();
                    $previousOrderId = Order::where('customer_id',$user->id)->where('is_completed','N')->first();// fetching previous order which is not complete
                    if($previousOrderId!=null)
                        $previousCartIds = Cart::where('order_id',$previousOrderId->id)->where('is_deleted','N')->pluck('id');//items added previously by user.
                    if($previousOrderId != null && count($previousCartIds)>0){
                        $guestCartsIds = Cart::where('order_id',$order_id)->where('is_deleted','N')->get();
                        foreach ($guestCartsIds as $key => $CartsIds){
                            //check for is same product with same configuration wes previously added, if yes then merge otherwise add guest cart to previuos order
                            $productCart =  Cart::whereIn('id',$previousCartIds)->where('product_id',$CartsIds->product_id)->where('configuration_id',$CartsIds->configuration_id)->where('is_deleted','N')->first();
                            if($productCart!=null){// if same conf. added product found
                                $totalQuantity = $productCart->quantity+$CartsIds->quantity;
                                //add the guest qty to previous cart added
                                Cart::where('id',$productCart->id)->update(array('quantity'=>$totalQuantity,'order_id'=>$productCart->order_id));
                                //delete guest cart
                                Cart::where('id',$CartsIds->id)->update(array('is_deleted'=>1));
                            }else{
                                //if same config not found then add this cart to previous order.
                                Cart::where('id',$CartsIds->id)->update(array('order_id'=>$previousOrderId->id));
                            }
                        }
                        //make guest order is is completed
                        Order::where('id',$order_id)->update(array('is_completed'=>'Y'));
                        //update total quantity
                        $getAllUpdatedCartQuantity = Cart::where("order_id",$previousOrderId->id)->sum("quantity");
                        Order::where('id',$previousOrderId->id)->update(array("total_cart_item"=>$getAllUpdatedCartQuantity));
                        session()->forget('order_id');//deleting order id from session
                    }else{
                        if(count($previousCartIds)<0){
                            Order::where('id',$previousOrderId->id)->update(array('customer_id'=>null));
                        }
                        Order::where('id',$order_id)->update(array('customer_id'=>$user->id));
                    }
                    session()->forget('order_id');//deleting order id from session
                }
                return 200;
            }
            return 403;

        }

        public function createToken(){
            $params = ['email' => env("SHIPROCKET_EMAIL"),'password' => env("SHIPROCKET_PASSWORD")];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,'https://apiv2.shiprocket.in/v1/external/auth/login');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response =json_decode( curl_exec($ch));
            return $response->token;
        }

        public function createOrder(array $data,$token){
            $curl = curl_init();
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = "Authorization: Bearer {$token}";
            curl_setopt_array($curl, [
                CURLOPT_URL =>"https://apiv2.shiprocket.in/v1/external/orders/create/adhoc",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER =>$headers,
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response);
        }

        public function trackOrder($shipment_id,$token){
            $curl = curl_init();
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = "Authorization: Bearer {$token}";
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/courier/track/shipment/$shipment_id",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER =>$headers,
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response);
        }
}
