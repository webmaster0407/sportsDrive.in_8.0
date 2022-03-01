<?php

namespace App\Http\Controllers\Admin;

use App\coupons;
use App\CouponsProducts;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Http\Requests;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CouponsController extends Controller
{
   
    public function listCoupons(){
        try{
            $coupons = Coupons::where("is_promotional","N")->get();
            return view('admin.list-coupons')->with(compact("coupons"));
            
        } catch (\Exception $ex) {
            $data = [
                'input_params' => null,
                'action' => 'Admin list Coupans',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
        
    }
    
     public function addCoupons(Request $request ,$id = null){
         try{
              if($id!=null){
                $data = coupons::where("id",$id)->first();
                $productIds = CouponsProducts::where("coupons_products.coupon_id",$id)->pluck("product_id")->toArray();
            }else{
                $data = array(
                    "id"=>"0",
                    "name" => "",
                    "code" => "",
                    "discount" => "",                   
                    "short_description" => "",
                    "mode" => "add",
                );
                $data = (object) $data;
                $productIds = array();
            }
            $products = Product::join("product_color_images_map","product_color_images_map.product_id","products.id")->join("attributes","product_color_images_map.color_id","=","attributes.id")->where([['products.is_active', '=', 'Y'], ['products.is_completed', '=', 'Y'], ['products.is_verified', '=', 'Y']])->select("products.id","products.name","attributes.name as attributeColor")->groupBy("products.id")->get()->toArray();
            return view('admin.add-coupons')->with(compact('data', 'products',"couponsProducts","productIds"));
         } catch (\Exception $ex) {
             $data = [
                 'input_params' => $request->all(),
                 'action' => 'addCoupons Coupons',
                 'exception' => $ex->getMessage()
             ];
             Log::info(json_encode($data));
             abort(500);
         }
    }
    
    public function addCouponData(Requests\Coupons $request ,$id = null){
        try{
            $data = $request->except("_token");
            $validator = Validator::make($data, [
                "name" => "required|string",
                "discount" => "required",
                "products" => "required",
                'code' => ['required',Rule::unique('coupons')->ignore($id),
                ],
            ]);
            if ($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $time = Carbon::now();
            $products = array();
            if(array_key_exists("products",$data)){
                $products = $data['products'];
                unset($data['products']);
            }
            if($id==0){ //add coupons data
                $data['created_at'] = $time;
                $data['updated_at'] = $time;
                $coupons = coupons::insertGetId($data);
                CouponsProducts::where("coupon_id",$id)->delete();//delete previous entries
                foreach($products as $product){
                    $productCoupons['product_id'] = $product;
                    $productCoupons['coupon_id'] = $coupons;
                    $productCoupons['created_at'] = $time;
                    $productCoupons['updated_at'] = $time;
                    CouponsProducts::insert($productCoupons);//insert new  entries
                }
                $message = "Coupon created successfully.";
            }else{ //Edit coupons data
                if($products!=null)
                    CouponsProducts::where("coupon_id",$id)->delete();//delete previous entries
                foreach($products as $product){
                    $productCoupons['product_id'] = $product;
                    $productCoupons['coupon_id'] = $id;
                    $productCoupons['created_at'] = $time;
                    $productCoupons['updated_at'] = $time;
                    CouponsProducts::insert($productCoupons);//insert new  entries
                }
                $coupons = coupons::where("id",$id)->update($data);
                $message = "Coupon updated successfully.";                
            }
              if($coupons){
                return redirect("/administrator/list-coupons")->with("success",$message);
            }else
                return redirect("/administrator/list-coupons")->with("error",$message);
        } catch (\Exception $ex) {
            $data = [
                'input_params' => $request->all(),
                'action' => 'Admin list Coupons',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);

        }
        
    }
    
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
                CouponsProducts::where("coupon_id",$couponIds)->delete();
                $message = "Coupons's successfully deleted.";
            }
            return redirect("/administrator/list-coupons")->with('success', $message);
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'Admin change Coupons Status',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
}
