<?php

namespace App\Http\Controllers\Admin;

use App\Cart;
use App\Offers;
use App\Product;
use App\ProductsOffers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Http\Requests;

class OffersController extends Controller
{
    public function __construct(){
        $this->middleware('adminauth');
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function listOffers(Request $request ) {
        try{
            $offers = Offers::all();
            return view('admin.list-offers')->with(compact("offers"));
        }catch(\Exception $e){
            $data = [
                'input_params' => $request,
                'action' => 'Admin list offers',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    /**
     * @param Request $request
     * @param null $id
     * @return $this
     */
    public function addOffers(Request $request ,$id = null) {
        try{
            if($id!=null){
                $data = Offers::where("id",$id)->first();
                $productIds = ProductsOffers::where("products_offers.offer_id",$id)->pluck("product_id")->toArray();
            }else{
                $data = array(
                    "id"=>"0",
                    "name" => "",
                    "meta_title" => "",
                    "meta_keyword" => "",
                    "meta_description" => "",
                    "short_description" => "",
                    "quantity" => "",
                    "discount" => "",
                    "shipping" => "",
                    "description" => "",
                    "mode" => "add",
                );
                $data = (object) $data;
                $productIds = array();
            }
            $products = Product::join("product_color_images_map","product_color_images_map.product_id","products.id")->join("attributes","product_color_images_map.color_id","=","attributes.id")->where([['products.is_active', '=', 'Y'], ['products.is_completed', '=', 'Y'], ['products.is_verified', '=', 'Y']])->select("products.id","products.name","attributes.name as attributeColor")->groupBy("products.id")->get()->toArray();
            return view('admin.add-offers')->with(compact('data', 'products',"productsOffers","productIds"));
        }catch(\Exception $e){
            $data = [
                'input_params' => $request,
                'action' => 'Admin list offers',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function addOffersData(Requests\Offers $request ,$id = null) {
        try{
            $time = Carbon::now();
            $data = $request->except("_token");
            $products = array();
            if(array_key_exists("products",$data)){
                $products = $data['products'];
                unset($data['products']);
            }
            if($id == 0){
                $data['created_at'] = $time;
                $data['updated_at'] = $time;
                $offers = Offers::insertGetId($data);
                ProductsOffers::where("offer_id",$id)->delete();//delete previous entries
                foreach($products as $product){
                    $productOffer['product_id'] = $product;
                    $productOffer['offer_id'] = $offers;
                    $productOffer['created_at'] = $time;
                    $productOffer['updated_at'] = $time;
                    ProductsOffers::insert($productOffer);//insert new  entries
                }
                $message = "Offer created successfully.";
            }else{
                if($products!=null)
                    ProductsOffers::where("offer_id",$id)->delete();//delete previous entries
                foreach($products as $product){
                    $productOffer['product_id'] = $product;
                    $productOffer['offer_id'] = $id;
                    $productOffer['created_at'] = $time;
                    $productOffer['updated_at'] = $time;
                    ProductsOffers::insert($productOffer);//insert new  entries
                }
                $offers = Offers::where("id",$id)->update($data);
                $message = "Offer updated successfully.";
            }
            if($offers){
                return redirect("/administrator/list-offers")->with("success",$message);
            }else
                return redirect("/administrator/list-offers")->with("error",$message);
        }catch(\Exception $e){
            $data = [
                'input_params' => $request,
                'action' => 'Admin list offers',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function changeOfferStatus(Request $request ,$id = null) {
        try{
            $data = $request->all();
            $operationFlag = $data['operationFlag'];
            $offerIds = $data['chk'];
            $message = "Something went wrong!Please try again";
            if ($operationFlag == 'active') {
                Offers::whereIn('id', $offerIds)->update(array('is_active' => 'Y'));
                $message = "Offer's successfully activated.";
            } else if ($operationFlag == 'deactive') {
                Offers::whereIn('id', $offerIds)->update(array('is_active' => 'N'));
                $message = "Offer's successfully deactivated.";
            } else if ($operationFlag == 'delete') {
                Offers::whereIn('id', $offerIds)->delete();
                ProductsOffers::where("offer_id",$offerIds)->delete();
                $message = "Offer's successfully deleted.";
            }
            return redirect("/administrator/list-offers")->with('success', $message);
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'Admin change Offer Status',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
}
