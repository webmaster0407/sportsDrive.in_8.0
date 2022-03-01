<?php

namespace App\Http\Controllers;

use App\Cart;
use App\coupons;
use App\CouponsProducts;
use App\CouponsUsers;
use App\Http\Controllers\CustomTraits\CartTrait;
use App\Http\Controllers\CustomTraits\OrderTrait;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CouponsController extends Controller
{
    public function __construct(){
        $this->middleware('checkVisitors');
    }

    use CartTrait;
    public function ApplyCoupon(Request $request){
        try{
            return $this->coupon($request);
        }catch (\Exception $e){
            $data = [
                'input_parms'=>$request->all(),
                'user'=>Auth::user(),
                'Exception'=>$e->getMessage()
            ];
            Log::info($data);
            abort(500);
        }
    }
}
