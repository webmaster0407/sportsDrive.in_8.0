<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Banner;
use App\Category;
use App\CmsPage;
use App\Product;
use App\ProductsOffers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function __construct(){
        $this->middleware('userauth')->except('index');
        $this->middleware('checkVisitors');
    }

    public function index()
    {
        try{
            $data = array();
            $footerPages = array();
            $servicePages = array();
            $bannerData = Banner::where("is_active","Y")->orderby("sort_order")->get();
            $featuredPages = CmsPage::where("is_active","Y")->where("is_featured","Y")->orderby("sort_order")->get();
            $aboutUs = CmsPage::where("is_active","Y")->where("slug","about-us")->first();
            $pages = CmsPage::where("is_active","Y")->get();
			$homePage = CmsPage::where("is_active","Y")->where("slug","home")->first();
            $topCategories = Category::where("is_active","Y")->where('is_top','Y')->orderby("sort_order")->get();
            $bottomCategories = Category::where("is_active","Y")->where('is_bottom','Y')->orderby("sort_order")->get();
            $featuredProduct = Product::where("is_active","Y")->where('is_featured','Y')->where('is_completed','Y')->where('is_verified','Y')->orderby("sort_order")->get();
            foreach($featuredProduct as $key => $product){
                $offer = ProductsOffers::join("offers","products_offers.offer_id","=","offers.id")->where("product_id",$product->id)->first();
                $featuredProduct[$key]['offer'] = $offer;
            }
            return view('user.home')->with(compact('data','bannerData','featuredPages','aboutUs','footerPages','pages','homePage','servicePages','topCategories','featuredProduct',"bottomCategories"));
        }catch(\Exception $e){
            $data = [
                'input_params' => NULL,
                'action' => 'front home',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
}
