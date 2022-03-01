<?php

namespace App\Http\Controllers\Admin;

use App\coupons;
use App\CouponsProducts;
use App\Customer;
use App\Order;
use App\Partner;
use App\PartnerCoupons;
use App\Slots;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PartnerSalesDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware('adminauth');
    }

    public function salesDashboard(Request $request, $id){
        try{
            $data = $this->getSalesData($id);
            $salesWindowsSum = $data['salesWindowsSum'];
            $partner = $data['partner'];
            $coupons = $data['coupons'];
            $getAllPartnerCoupons = $data['getAllPartnerCoupons'];
            $salesWindows = $data['salesWindows'];
            $partnerSalesWindowOrders = $data['partnerSalesWindowOrders'];
            return view('admin.partner-sales.list')->with(compact("salesWindowsSum","partner",'getAllPartnerCoupons','coupons',"salesWindows","partnerSalesWindowOrders"));
        }catch(\Exception $e){
            $data = [
                'input_params' => $request,
                'action' => 'Admin sales Dashboard',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }


    public function salesDetails(Request $request, $id,$slot_id = null){
        try{
            $data = $this->getSalesData($id);
            $salesWindowsSum = $data['salesWindowsSum'];
            $partner = $data['partner'];
            $coupons = $data['coupons'];
            $getAllPartnerCoupons = $data['getAllPartnerCoupons'];
            $salesWindows = $data['salesWindows'];
            
            $currentSalesWindow = $data['currentSalesWindow'];
            $partnerSalesWindowOrders = $data['partnerSalesWindowOrders'];
            $partnerSalesWindowCurrentOrders = [];
            $orderWisePrice = array();
            $orderWiseQty = array();
            if($slot_id == null && array_key_exists($currentSalesWindow['id'],$partnerSalesWindowOrders)){
                $currentSalesOrders = $partnerSalesWindowOrders[$currentSalesWindow['id']];
                $slot_id = $currentSalesWindow['id'];
            }else if($slot_id != null){
                $currentSalesOrders = $partnerSalesWindowOrders[$slot_id];
            }else{
                $currentSalesOrders = array();
            }
            foreach ($currentSalesOrders as $key=>$cart){
                $cart['product_gst'] == null ? $gst = 0 : $gst = $cart['product_gst'];
                strlen($gst) == 1 ? $gstdevider = "10".$gst : $gstdevider = "1".$gst;
                $currentSalesOrders[$key]['price_without_gst'] = floor(($cart['final_price'] / $gstdevider) * 100);
            }
            return view('admin.partner-sales.details')->with(compact("slot_id","id","orderWiseQty","currentSalesOrders","salesWindowsSum","partner",'getAllPartnerCoupons','coupons',"salesWindows","partnerSalesWindowOrders","partnerSalesWindowCurrentOrders","currentSalesOrders","orderWisePrice"));
        }catch(\Exception $e){
            $data = [
                'input_params' => $request,
                'action' => 'Admin sales Dashboard',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function getSalesData($id){
        $partner = Partner::findOrFail($id);
        $partnerSalesWindowOrders = array();
        $date = date("Y-m-d");
        $coupons = array();
        $salesWindows = array();
        $currentSalesWindow = Slots::where('start_date','<=',$date)->where('end_date','>=',$date)->first()->toArray();
        $salesWindows = Slots::where("id","<=",$currentSalesWindow['id'])->orderBy("id","desc")->take(4)->get();
        if(count($currentSalesWindow) > 0){
            $getAllPartnerCoupons = PartnerCoupons::where("partner_id",$id)->pluck("coupon_id")->toArray();
            if($getAllPartnerCoupons != null){
                $coupons = coupons::whereIn("id",$getAllPartnerCoupons)->pluck("code","id")->toArray();
                $couponsIds = array_keys($coupons);
                $couponAssignedProducts = CouponsProducts::whereIn("coupon_id",$couponsIds)->pluck("product_id")->toArray();
                $OrderIds = Order::whereIn("coupon_code",$coupons)->whereIn("order_status",[2,13])->orderby("created_at", "DESC")->pluck("id")->toArray();
                if(count($OrderIds)>0 && count($couponAssignedProducts)>0) {
                    foreach ($salesWindows as $salesWindow){
                        $partnerSalesWindowOrders[$salesWindow->id] = Order::whereIn("orders.id",$OrderIds)->whereBetween('orders.order_date', [$salesWindow->start_date, $salesWindow->end_date])->join("carts","carts.order_id","=","orders.id")
                            ->join("products","products.id","=","carts.product_id")
                            ->whereIn("carts.product_id",$couponAssignedProducts)
                            ->where("carts.is_deleted","N")
                            ->join("customers","customers.id","=","orders.customer_id")
                            ->select("carts.*","products.gst as product_gst","orders.shipping_address as shipping_address","customers.first_name","customers.last_name","orders.order_date","products.name as product_name")
                            ->get()
                            ->toArray();
                    }
                }
            }
            $salesWindowsSum = array();
            foreach ($partnerSalesWindowOrders as $key=>$partnerSalesWindowOrder) {
                $totalItem = 0;
                $totalSale = 0;
                foreach ($partnerSalesWindowOrder as $cart){
                    $cart['product_gst'] == null ? $gst = 0 : $gst = $cart['product_gst'];
                    $totalItem = $totalItem + $cart['quantity'];
                    strlen($gst) == 1 ? $gstdevider = "10".$gst : $gstdevider = "1".$gst;
                    $cartPriceLessGST = ($cart['final_price'] / $gstdevider) * 100;
                    $totalSale = $totalSale + $cartPriceLessGST;
                }
                $salesWindowsSum[$key]['totalUnitSold'] = $totalItem;
                $salesWindowsSum[$key]['totalSale'] = $totalSale;
                if($partner['flat_comission'] == null){
                    if($salesWindowsSum[$key]['totalUnitSold'] > 71){
                        $salesWindowsSum[$key]['totalCommission'] = ($salesWindowsSum[$key]['totalSale']/100)*10;
                    }else if($salesWindowsSum[$key]['totalUnitSold'] > 35 && $salesWindowsSum[$key]['totalUnitSold'] <= 71){
                        $salesWindowsSum[$key]['totalCommission'] = ($salesWindowsSum[$key]['totalSale']/100)*7.5;
                    }else {
                        $salesWindowsSum[$key]['totalCommission'] = ($salesWindowsSum[$key]['totalSale']/100)*5;
                    }
                }else{
                    $salesWindowsSum[$key]['totalCommission'] = ($salesWindowsSum[$key]['totalSale']/100)*$partner['flat_comission'];
                }
            }
            $returnData  = array(
                "salesWindowsSum" => $salesWindowsSum,
                "partner" => $partner,
                'getAllPartnerCoupons' => $getAllPartnerCoupons,
                'coupons' => $coupons,
                "salesWindows" => $salesWindows,
                "partnerSalesWindowOrders" => $partnerSalesWindowOrders,
                "currentSalesWindow" => $currentSalesWindow,
            );
            return $returnData;
        }else{
            abort(500,"Current Sales window is not set, Please create the sales window first.");
        }
    }
}
