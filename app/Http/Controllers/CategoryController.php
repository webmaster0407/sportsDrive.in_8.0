<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function __construct(){
        $this->middleware('checkVisitors');
    }
    public function ListShopByCategories(){
        try{
            $categories = Category::where("is_active","Y")->where('is_header','Y')->where('level_id',0)->orderby("sort_order")->get()->toArray();
            foreach ($categories as $key=>$category){//get sub categories
                $subSubCount = 0;
                $subCategories = Category::where("is_active","Y")->where('is_header','Y')->where('level_id',1)->where('parent_id',$category['id'])->orderby("sort_order")->get()->toArray();
                $categories[$key]['sub_categories'] = $subCategories;
                foreach($subCategories as $subCatKey => $subCategory){
                    $subSubCategories = Category::where("is_active","Y")->where('is_header','Y')->where('level_id',2)->where('parent_id',$subCategory['id'])->orderby("sort_order")->get()->toArray();
                    $categories[$key]['sub_categories'][$subCatKey]['subSubCategories'] = $subSubCategories;
                    $subSubCount =    $subSubCount+count($subSubCategories);
                }
                $categories[$key]['sub_sub_categories_count'] = $subSubCount;
            }
            return view("user.shop-by-category")->with(compact("categories"));
        }catch(\Exception $e){
            $data = [
                'input_params' => null,
                'action' => 'ListShopByCategories',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
}
