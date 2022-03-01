<?php

namespace App\Http\Controllers\Admin;

use App\Address;
use App\Admin;
use App\Category;
use App\coupons;
use App\Customer;
use App\CustomersGroups;
use App\CustomersGroupsUsers;
use App\Jobs\SendEmailToCouponUsers;
use App\Jobs\SendProductPromotionEmail;
use App\Order;
use App\Product;
use App\ProductPromotions;
use App\PromotionsCoupons;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Http\Requests;

class ProductPromotionController extends Controller
{

    public function __construct()
    {
        $this->middleware('adminauth');
    }

    /**
     * Display a listing of the coupons.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $productPromotions = ProductPromotions::orderBy("created_at","DESC")->get();
            return view('admin.product-promotions.product-promotions')->with(compact("productPromotions"));
        } catch (\Exception $ex) {
            $data = [
                'input_params' => null,
                'action' => 'Admin list Promotional Coupons',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request ,$id = null)
    {
        try{
            if($id != null){
                $data = ProductPromotions::where("id",$id)->first();
            }else{
                $data = array(
                    "id"=>"0",
                    "name" => "",
                    "code" => "",
                    "product_ids" => array(),
                    "promotion_type" => "",
                    "short_description" => "",
                    "mode" => "add",
                    "banner_image" => "",
                );
                $data = (object) $data;
            }
            $productIds = Product::where("is_active",'Y')->get();
            $categories = $this->getAllCategories();
            $selectedMainCategories = array();
            $selectedSubCategories= array();
            $selectedSubSubCategories= array();
            $customers_cities = Address::distinct('city')->pluck("city");
            return view('admin.product-promotions.add-product-promotion')->with(compact('data',"productIds", "productIds","categories","selectedMainCategories","selectedSubCategories","selectedSubSubCategories","customers_cities"));
        } catch (\Exception $ex) {
            $data = [
                'input_params' => $request->all(),
                'action' => 'add Promotional Coupons',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    /**
     * @return mixed
     */
    public function getAllCategories(){
        $mainCategories = $this->getInnerCategory(0);
        foreach ($mainCategories as $key => $value) {
            $subCategories =  $this->getInnerCategory($value['id']);
            if(count($subCategories) > 0)
                $mainCategories[$key]["subCategories"] = $subCategories;
            else
                $mainCategories[$key]["subCategories"] = null;
        }
        foreach ($mainCategories as $mainCategoriesKey => $subCategories) {
            if($subCategories['subCategories']!=null){
                foreach ($subCategories['subCategories'] as $subCategoriesKey => $subSubCategories) {
                    $subSubCategories =  $this->getInnerCategory($subSubCategories['id']);
                    if(count($subSubCategories)>0)
                        $mainCategories[$mainCategoriesKey]["subCategories"][$subCategoriesKey]['subSubCategories'] = $subSubCategories;
                    else
                        $mainCategories[$mainCategoriesKey]["subCategories"][$subCategoriesKey]['subSubCategories'] = null;
                }
            }
        }
        return $mainCategories;
    }

    /**
     * @param $catID
     * @return mixed
     */
    public function getInnerCategory($catID) {
        $category = Category::select('id', 'name', 'level_id')
            ->where([["parent_id", $catID], ["is_active", "Y"]])
            ->get();
        $category = $category->each(function ($item, $key) {
            if (is_array($item) && count($item) > 0 && $item != null) {
                $category[$key]= $item;
            }
        });
        return $category;
    }

    public function store(Requests\AddProductPromotionData $request,$id = null) {
        try{
            $data = $request->except("_token");
            $file = $request->file('banner_image');
            if($file){
                $ds = DIRECTORY_SEPARATOR;
                $banner_image_name = uniqid()."".$request->file('banner_image')->getClientOriginalName();
                $destinationPath_icon =  public_path().$ds."uploads".$ds.'product_promotions'.$ds;
                if (!file_exists($destinationPath_icon)) {
                    File::makeDirectory($destinationPath_icon, $mode = 0777, true, true);
                }
                $file->move($destinationPath_icon, $banner_image_name);
            }else{
                $banner_image_name= "";
            }
            $time = Carbon::now();
            if($data['promotion_type'] == "P")
                $promotion_type_ids	 = implode(",",$data['product_ids']);
            else
                $promotion_type_ids = implode(",",$data['categories']);
            $promotionData['promotion_type_ids'] = $promotion_type_ids;
            $promotionData['banner_image'] = $banner_image_name;
            $promotionData['promotion_type'] = $data['promotion_type'];
            $promotionData['short_description'] = $data['short_description'];
            $promotionData['created_at'] = $time;
            $promotionData['updated_at'] = $time;
            if($data['coupons_for'] == 1){ //for selected cities
                $address = Address::query();
                foreach($data['customers_cities'] as $city){
                    $address->orWhere('city', 'LIKE', '%'.$city.'%');
                }
                $customer_id = $address->distinct()->pluck("customer_id")->toArray();
                $customers = Customer::where("is_active",'Y')->whereIn("id",$customer_id)->get();
            }else{
                $customers = User::where("is_active","Y")->get();
            }
            $promotionId = ProductPromotions::insertGetId($promotionData);
            dispatch(new SendProductPromotionEmail($promotionId,$customers));
            $message = "Promotions Emails will be sent to selected customers in sometime.";
            return redirect("/administrator/list-product-promotions")->with('success', $message);
        }catch (\Exception $ex) {
            $data = [
                'input_params' => $request->all(),
                'action' => 'Admin store Coupons',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);

        }
    }
}
