<?php
namespace App\Http\Controllers\CustomTraits;

use App\Category;
use App\PColorImagesMap;
use App\Product;
use App\ProductCategoryMap;
use App\ProductFor;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\RatingReviews;
use Intervention\Image\Facades\Image;
use App\Brand;

trait ProductTrait{
    public function getReviewRatingList($productID){
        try{
            $ratingReviews = RatingReviews::where('product_id',$productID)->orderBy('created_at','DESC')->paginate(2);
            return  $ratingReviews;

        }catch (\Exception $e){
            $data = [
                'productID' =>$productID,
                'action' => 'get ReviewRatingList in traits',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500,$e->getMessage());
        }
    }
    public function getFilteredProducts($finalFilters,$productIds){
        try{
            $orderBy = "sort_order";
            $sortOrderType="DESC";
            $take = ENV("pagination_result");
            if(array_key_exists("pp",$finalFilters))//check for result per page
                $take = $finalFilters['pp'];

            if(array_key_exists("sortBy",$finalFilters)) {// check for sort by
                $orderBy = "price";
                if($finalFilters['sortBy']=="l2h")
                    $sortOrderType="ASC";
                elseif($finalFilters['sortBy']=="h2l")
                    $sortOrderType ="DESC";
                else{
                    $orderBy = "created_at";
                    $sortOrderType="ASC";
                }
            }

            $allProductIdsData = explode(",",$productIds);
            if(array_key_exists("f",$finalFilters)) {//check for like men, women, girl or boys
                $forArray = explode(",",$finalFilters['f']);
                $allProductIdsData = ProductFor::whereIn("for", $forArray)
                    ->whereIn("product_id", $allProductIdsData)
                    ->pluck("product_id");
            }
            if(array_key_exists("b",$finalFilters)) {//check for brands
                    $brandArray = explode(",",$finalFilters['b']);
                     if($brandArray != null)
                    $allProductIdsData = Product::whereIn("brand_id", $brandArray)->whereIn("id", $allProductIdsData)->pluck("id")->toArray();
                }
            $categoryArray = null;
            if(array_key_exists("sc",$finalFilters) && array_key_exists("ssc",$finalFilters) ){
                $categoryArray = array_merge(explode(",",$finalFilters['sc']),explode(",",$finalFilters['ssc']));
            }elseif(array_key_exists("sc",$finalFilters)){
                $categoryArray = explode(",",$finalFilters['sc']);
            }elseif(array_key_exists("ssc",$finalFilters)){
                $categoryArray = explode(",",$finalFilters['ssc']);
            }

            if($categoryArray != null)
                $allProductIdsData = ProductCategoryMap::whereIn("category_id",$categoryArray)->whereIn("product_id",$allProductIdsData)->pluck("product_id");
            $query = Product::where("is_active","Y")
                ->where('is_completed','Y')
                ->where('is_verified','Y');

            if(array_key_exists("p",$finalFilters)){
                $priceArray = explode(",",$finalFilters['p']);
                foreach($priceArray as $key=>$price){
                    $prices= explode("-",$price);
                    $query->whereBetween("price",$prices);
                    $query->orWhere(function ($query) use ($prices) {
                        $query->where('price', '<=', $prices[1]);
                        $query->where('price', '>=', $prices[0]);
                    });
                }
            }
            $products = $query->pluck("id");
            $data = [
                'products'=>$products,
                'orderBy'=>$orderBy,
                'sortOrderType'=>$sortOrderType,
                'allProductIdsData'=>$allProductIdsData,
                "take"=>$take,
            ];
            return $data;
        }catch (\Exception $e){
            $data = [
                'finalFilters' => $finalFilters,
                'productIds' => $productIds,
                'action' => 'Get filtered products in traits',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500,$e->getMessage());
        }
    }
    function getSelectedFiltersData($finalFilters){
        $select = null;
        if(array_key_exists("f",$finalFilters)){
            $for = explode(",",$finalFilters['f']);
            foreach($for as $val){
                if($val=="m"){
                    $select .= "<li><i class='fa fa-times' data-id='f_m' aria-hidden='true'></i>Men</li>";
                }elseif($val=="w"){
                    $select .= "<li><i class='fa fa-times' data-id='f_w' aria-hidden='true'></i>Women</li>";
                }elseif($val=="g"){
                    $select .= "<li><i class='fa fa-times' data-id='f_g' aria-hidden='true'></i>Girls</li>";
                }elseif($val=="b"){
                    $select .= "<li><i class='fa fa-times' data-id='f_b' aria-hidden='true'></i>Boys</li>";
                }
            }
        }
        if(array_key_exists("p",$finalFilters)){
            $priceArray = explode(",",$finalFilters['p']);
            foreach($priceArray as $key=>$price){
                $select .= "<li><i class='fa fa-times'  data-id='p_".$price."' aria-hidden='true'></i>".$price."</li>";
            }
        }
        if(array_key_exists("b",$finalFilters)){
            $brandArray = explode(",",$finalFilters['b']);
            $names = Brand::whereIn("id",$brandArray)->select("name","id")->get()->toArray();
            foreach($names as $name){
                $select .= "<li><i class='fa fa-times'  data-id='b_".$name['id']."' aria-hidden='true'></i>".$name['name']."</li>";
            }
        }
        if(array_key_exists("sc",$finalFilters)){
            $categoryArray = explode(",",$finalFilters['sc']);
            $names = Category::whereIn("id",$categoryArray)->select("name","id")->get()->toArray();
            foreach($names as $name){
                $select .= "<li><i class='fa fa-times'  data-id='sc_".$name['id']."' aria-hidden='true'></i>".$name['name']."</li>";
            }
        }
        if(array_key_exists("ssc",$finalFilters)){
            $categoryArray = explode(",",$finalFilters['ssc']);
            $names = Category::whereIn("id",$categoryArray)->select("name","id")->get()->toArray();
            foreach($names as $name){
                $select .= "<li><i class='fa fa-times' data-id='ssc_".$name['id']."'  aria-hidden='true'></i>".$name['name']."</li>";
            }
        }

        return $select;
    }

    function removeImagesFromStorage($product_id,$imgName){
        $ds = DIRECTORY_SEPARATOR;
        $path = public_path().$ds."uploads".$ds.'products'.$ds.'images'.$ds.$product_id;
        $imagePath1 = $path.$ds.$imgName;
        $imagePath2 = $path.$ds.'80x85'.$ds.$imgName;
        $imagePath4 = $path.$ds.'250x250'.$ds.$imgName;
        $imagePath5 = $path.$ds.'500x500'.$ds.$imgName;
        $imagePath6 = $path.$ds.'1024x1024'.$ds.$imgName;
        if(file_exists($imagePath1))
                unlink($imagePath1);
        if(file_exists($imagePath2))
                unlink($imagePath2);
        if(file_exists($imagePath4))
                unlink($imagePath4);
        if(file_exists($imagePath5))
                unlink($imagePath5);
        if(file_exists($imagePath6))
                unlink($imagePath6);
        return true;
    }
}
