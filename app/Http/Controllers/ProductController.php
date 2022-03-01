<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Controllers\CustomTraits\ProductTrait;
use App\Offers;
use App\Product;
use App\ProductCategoryMap;
use App\ProductFor;
use App\ProductsOffers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Attribute;
use App\AttributeGroup;
use App\ProductConfiguration;
use App\ConfigImage;
use App\PCImagesMap;

use App\UserCartMap;
use App\Cart;
use App\RatingReviews;
use Illuminate\View\View;
use App\Http\Requests;
use App\PColorImagesMap;
use App\Brand;


class ProductController extends Controller
{
    /*used Traits*/
    use ProductTrait;
    public function __construct(){
        $this->middleware('checkVisitors');
    }
    public function searchBrandList(Request $request){
         try{   
                $data = $request->all();
                $searchText = $data['key']; 
                $string ="";
                $brands = Brand::where("name",'LIKE',"%$searchText%")->get();
                foreach($brands as $brand){
                    $string .= '<li>';
                    $string .= '<div class="custome-checkbox">';
                    $string .= '<input class="form-check-input filter-field-brand" type="checkbox" id="b_'.$brand['id'].'" name="select" value="'.$brand['id'].'" data-name="'.$brand['name'].'">';
                    $string .= '<label class="form-check-label" for="b_'.$brand['id'].'"><span>'.$brand['name'].'</span></label>';
                    $string .= '</div>';
                    $string .= '</li>';
                }
                return $string;
            }catch(\Exception $e){
                $data = [
                    'input_params' => $data,
                    'action' => 'searchBrandList',
                    'exception' => $e->getMessage()
                ];
                Log::info(json_encode($data));
                abort(500);
            }
    }

    public function ListCategoryProduct(Requests\ListCategoryProductRequest  $request,$category){
        try{
            $data  = $request->all();
            if(!array_key_exists("page", $data)){
                $url = $request->getUri();
                $url = $url . "?page=1";
                return redirect($url);
            }
            $brands = Brand::where('is_active', 'Y')->get();
            $orderBy = "sort_order";
            $sortOrderType ="DESC";
            $category = Category::where('slug',$category)->first();
            $pagination = env("pagination_result");
            $allProductIdsData 
                = $subCategories 
                = $subSubCategories 
                = $subCategoriesIds 
                = $productIds 
                = $subSubCategoriesIds 
                = $subCategoriesProductIds 
                = $subSubCategoriesProductIds 
                = array();
            $productIds = ProductCategoryMap::where("category_id", $category->id)->pluck("product_id")->toArray();

            if($category->level_id == 0 ) {//if the category is main parent then fetch all the products of sub subcategories
                $subCategoriesIds = Category::where('parent_id', $category->id)
                        ->pluck("id");
                $subCategoriesProductIds = ProductCategoryMap::whereIn("category_id", $subCategoriesIds)
                        ->pluck("product_id")->toArray();
                $subSubCategoriesIds = Category::whereIn('parent_id',$subCategoriesIds)
                        ->pluck("id");
                $subSubCategoriesProductIds = ProductCategoryMap::whereIn("category_id",$subSubCategoriesIds)->pluck("product_id")->toArray();
            }elseif($category->level_id==1){// if the category is at level one
                $subSubCategoriesIds = Category::where('parent_id',$category->id)->pluck("id");
                $subSubCategoriesProductIds = ProductCategoryMap::whereIn("category_id",$subSubCategoriesIds)->pluck("product_id")->toArray();
            }
            //make object list to array to merge
            $allProductIdsData = array_unique(
                    array_merge(
                        $allProductIdsData,
                        $productIds,
                        $subCategoriesProductIds,
                        $subSubCategoriesProductIds
                    )
                );

            //get category products Filters
            if( count( $subCategoriesIds ) > 0 )
                $subCategories = Category::whereIn('id', $subCategoriesIds)
                        ->where("is_active","Y")
                        ->select("id","name")
                        ->get();
            if( count( $subSubCategoriesIds ) > 0 )
                $subSubCategories = Category::whereIn('id',$subSubCategoriesIds)
                        ->where("is_active","Y")
                        ->select("id","name")
                        ->get();
            $selected = null;
            
            if ( $data !== null) {//user refreshed page with selected filters to hits directly filter range
                $finalFilters = array();
                foreach ($data as $key=>$filter){
                    $finalFilters[$key] = $filter;
                }

                /* to make selected above filters starts here*/
                $selected = $this->getSelectedFiltersData($finalFilters);
                /* to make selected above filters starts here*/

                $orderBy = "sort_order";
                $sortOrderType="DESC";
                $take = ENV("pagination_result");
                if( array_key_exists("pp", $data) )//check for result per page
                    $take = $data['pp'];

                if(array_key_exists("sortBy", $data)) {// check for sort by
                    $orderBy = "price";
                    if( $data['sortBy'] == "l2h")
                        $sortOrderType="ASC";
                    elseif( $data['sortBy'] == "h2l")
                        $sortOrderType ="DESC";
                    else{
                        $orderBy = "is_new";
                        $sortOrderType="ASC";
                    }
                }

                if(array_key_exists("b", $data)) {//check for brands
                    $brandArray = explode(",", $data['b']);
                     if($brandArray !== null)
                    $allProductIdsData = Product::whereIn("brand_id", $brandArray)->whereIn("id", $allProductIdsData)->pluck("id")->toArray();
                }
                
                if (array_key_exists("f", $data)) {//check for like men, women, girl or boys
                    $forArray = explode(",", $data['f']);
                    $allProductIdsData = ProductFor::whereIn("for", $forArray)
                                                ->whereIn("product_id", $allProductIdsData)
                                                ->pluck("product_id")
                                                ->toArray();
                }

                $categoryArray = null;
                if (array_key_exists("sc", $data) 
                    && array_key_exists("ssc", $data) ) {
                    $categoryArray = array_merge(
                        explode(",", $finalFilters['sc'] ),
                        explode(",", $finalFilters['ssc'])
                    );
                } elseif (array_key_exists("sc", $data)) {
                    $categoryArray = explode(",", $finalFilters['sc']);
                } elseif (array_key_exists("ssc", $data)) {
                    $categoryArray = explode(",", $finalFilters['ssc']);
                }

                if($categoryArray !== null)
                    $allProductIdsData = ProductCategoryMap::whereIn("category_id", $categoryArray)
                                            ->whereIn("product_id", $allProductIdsData)
                                            ->pluck("product_id");
                $query = Product::where("is_active", "Y")
                                ->where('is_completed', 'Y')
                                ->where('is_verified', 'Y');

                if (array_key_exists("p", $data)){
                    $priceArray = explode(",", $data['p']);
                    foreach ($priceArray as $key => $price ) { 
                        $prices = explode("-", $price);
                        $query->whereBetween("price", $prices);
                        $query->orWhere(function ($query) use ($prices) {
                            $query->where('price', '<=', $prices[1]);
                            $query->where('price', '>=', $prices[0]);
                        });
                    }
                }
                $products = $query->pluck("id");
                $allProducts = Product::whereIn("id", $products) 
                        ->where("is_active", "Y")
                        ->where('is_completed', 'Y')
                        ->orderby($orderBy, $sortOrderType)
                        ->whereIn("id", $allProductIdsData)
                        ->get();
                $products = Product::whereIn("id", $products)
                        ->where("is_active", "Y")
                        ->where('is_completed', 'Y')
                        ->orderby($orderBy, $sortOrderType)
                        ->whereIn("id", $allProductIdsData)
                        ->paginate($take);
                $productsMax = $allProducts->max("price");
                $productsMin = $allProducts->min("price");
                $productsAvg = $allProducts->avg("price");
            } else {
                $allProducts = Product::whereIn("id", $allProductIdsData)
                    ->where("is_active", "Y")
                    ->where('is_completed', 'Y')
                    ->orderby($orderBy, $sortOrderType)
                    ->get();
                $productsMax = $allProducts->max("price");
                $productsMin = $allProducts->min("price");
                $productsAvg = $allProducts->avg("price");
                $products = Product::whereIn("id", $allProductIdsData)
                    ->where("is_active", "Y")
                    ->where('is_completed', 'Y')
                    ->orderby($orderBy, $sortOrderType)
                    ->paginate($pagination);
            }
            $links = $products->links();

            foreach ($products as $key => $product) {
                $offer = ProductsOffers::join("offers", "products_offers.offer_id", "=", "offers.id")
                        ->where("offers.is_active", "Y")
                        ->where("product_id", $product->id)
                        ->first();
                $products[$key]['offer'] = $offer;
            }
            return view("user.product-list")
                ->with(
                    compact(
                        "products",
                        "category",
                        "subCategories",
                        "subSubCategories",
                        "allProductIdsData",
                        "selected",
                        "brands",
                        "productsMax",
                        "productsMin",
                        "productsAvg", 
                        "links"
                    )
                );
        }catch(\Exception $e){
            $data = [
                'input_params' => NULL,
                'action' => 'ListCategoryProduct',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function filterProduct(Request $request){
        try{
            $data = $request->all();
            $finalFilters = array();
            $filters = explode('?', $data['filterPara']);
            $filters = explode('&', $filters[1]);
            foreach ($filters as $filter){
                    $filter = explode('=', $filter);
                    $finalFilters[$filter[0]] = $filter[1];
            }
           /* to make selected above filters starts here*/
            $selected = $this->getSelectedFiltersData($finalFilters);
            /* to make selected above filters starts here*/
            $finalData = $this->getFilteredProducts($finalFilters, $data['product_ids']);
            $allProducts = Product::whereIn("id", $finalData['products'])
                    ->where("is_active", "Y")
                    ->where('is_completed', 'Y')
                    ->whereIn("id", $finalData['allProductIdsData'])
                    ->pluck("id");
            $products =  Product::whereIn("id", $allProducts)
                ->orderby(
                    $finalData['orderBy'],
                    $finalData['sortOrderType']
                )->paginate($finalData['take']);
            $paginationCount = 0;
            if(count($allProducts) > count($products)){
                $paginationCount = 1;
            }
            $pagination = $products->links();
            

            $ds = DIRECTORY_SEPARATOR;
            $productData = null;

            foreach($products as $product) {
                /*'uploads/products/images/'.$product->id.'/1024x1024/'.$product->image*/
                $filePath = $ds . "uploads" . $ds . "products" . $ds . "images" . $ds . $product['id'] . $ds. "250x250". $ds . $product['image'];
                $fileFullPath = public_path() . $ds . "uploads" . $ds . "products" . $ds . "images" . $ds . $product['id'] . $ds. "250x250". $ds . $product['image'];
                $price = number_format($product->price - $product->discount_price,2);
                $offer = $offer = ProductsOffers::join("offers","products_offers.offer_id","=","offers.id")
                        ->where("offers.is_active","Y")
                        ->where(
                            "product_id",$product->id
                        )->first();
                // begin product container
                $productData .= '<div class="col-md-4 col-6">';

                // begin product
                $productData .= '<div class="product">';
                if ( $product->icon !== null ) {
                    $productData .= '<span class="pr_flash">';
                    $productData .= "<img src='/uploads/product_icon/".$product->id."/".$product->icon."'>";
                    $productData .= '</span>';
                }

                // begin product_img
                $productData .= '<div class="product_img">';
                if (file_exists( $fileFullPath ) )
                    $productData .= '<a href="/product/details/' . $product->slug . '"><img src="'. $filePath .'" alt="product"></a>';
                else
                    $productData .= '<a href="/product/details/' . $product->slug . '"><img src="/images/no-image-available.png" alt="product"></a>';

                $productData .= '<div class="product_action_box">
                                        <ul class="list_none pr_action_btn">
                                            <li class="add-to-cart"><a href="/product/details/' . $product->slug. '"><i class="icon-basket-loaded"></i> Add To Cart</a></li>
                                        </ul>
                                 </div>';

                $productData .= '</div>';   
                // end of product_img

                // begin product_info
                $productData .= '<div class="product_info">';

                    // begin product_title
                    $productData .= '<h6 class="product_title"><a href="/product/details/' . $product->slug . '">' . $product->name . '</a></h6>';
                    // end product_title

                    // begin product_price
                    $productData .= '<div class="product_price">';

                    $priceVal = number_format( ($product->price - $product->discount_price), 2 );
                    $productData .= ('<span class="price"> &#8377 ' . $priceVal . '</span>');

                    if ($product->price !== $product->price - $product->discount_price){
                        $originalPrice =  number_format($product->price, 2);
                        $productData .= ('<del>&#8377 ' . $originalPrice . '</del>');
                        $productData .= '<div class="on_sale">';
                        if($product->offer !=null) {
                            $offerDis = $product->offer['discount'];
                            $productData .= '<span>( $offerDis OFF )</span>
                                                <p>*Any color</p>';
                        }
                        $productData .= '</div>';
                    }

                    $productData .= '</div>';
                    // end product_price


                    // begin rating_wrap
                    $productData .= '<div class="rating_wrap">
                                            <div class="rating">
                                                <div class="product_rate" style="width:100%"></div>
                                            </div>
                                            <span class="rating_num"></span>
                                    </div>';
                    // end rating_wrap

                    // begin pr_desc
                    $productData .= '<div class="pr_desc">';
                    if($product['video_url']!=null){
                        $vURL = $product['video_url'];
                        $productData .= "<a class='playI' data-vid='$vURL' id='youtube'  data-toggle='modal' data-target='#youtube_video' data-keyboard='true' href='#'>";
                        $productData .= "<img src='/images/you_tube.png'><p>Click to watch product video</p></a>";
                    }

                    $productData .= "<p>$product->short_description</p>";
                    $productData .= '</div>';
                    // end pr_desc

                    // begin pr_switch_wrap
                    // $productData .= '<div class="pr_switch_wrap">
                    //                         <div class="product_color_switch">
                    //                             <span class="active" data-color="#87554B"></span>
                    //                             <span data-color="#333333"></span>
                    //                             <span data-color="#DA323F"></span>
                    //                         </div>
                    //                     </div>';
                    // end pr_switch_wrap

                    // begin list_product_action_box
                    $productData .= '<div class="list_product_action_box">
                                            <ul class="list_none pr_action_btn">
                                                <li class="add-to-cart"><a href="/product/details/' . $product->slug . '"><i class="icon-basket-loaded"></i> Add To Cart</a></li>
                                            </ul>
                                        </div>';
                    // end list_product_action_box

                $productData .= '</div>';
                // end product_info

                $productData .= '</div>';
                // end product

                $productData .= '</div>';
                // end product container
            }
            
            if ( $productData === null ) {
                $productData ='<div class="col-md-4 col-6"><h1 style="color: #0b3e6f">Sorry! No products found for this filter range.</h1></div>';
            }

            $pagination = "$pagination";
            $finalData = [
                'links' => $pagination,
                'productData' => $productData,
                "selected" => $selected,
                'paginationCount' => $paginationCount
            ];
            return $finalData;
        }catch(\Exception $e){
            $data = [
                'input_params' => $finalFilters,
                'action' => 'filterProduct',
                'exception' => $e->getMessage(),
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    /**
     * @param Request $request
     * @param $slug
     * @return $this
     */
    
    public function productDetails(Request $request, $slug){
        try{
                $user = Auth::user();
                $flag = false;
                $productOffers = array();
                $product = Product::where('slug',$slug)->first();
                /*check the product is_active,is_completed or not #start modified on 20/08/2018*/
                    if($product->is_active != "Y" || $product->is_completed != "Y" ){
                       return redirect("/");
                    }
                /*check the product is_active,is_completed or not #start modified on 20/08/2018*/
                if($user !== null){
                    $ispresent = RatingReviews::where('product_id', $product->id)
                            ->where('email', $user->email_address)
                            ->first();
                    if( $ispresent != null){
                        $flag = true;
                    } else {
                        $flag = false;
                    }
                }
                $finalprice = 0;
                if( $product['discount_price'] !== null){
                    $finalprice = $product['price'] - $product['discount_price'];
                }else{
                    $finalprice = $product['price'];
                }

                $productConfiguration = PColorImagesMap::where('product_id', $product->id)->get();
                $getattributesSize = ProductConfiguration::select(
                        'product_configuration.AttributeSize',
                        'attributes.name',
                        'product_configuration.quantity'
                    )->where('product_id', $product->id)
                    ->join('attributes','attributes.id', '=' , 'product_configuration.AttributeSize')
                    ->get();
                $sizeCollection = collect($getattributesSize);
                $uniqueSize = $sizeCollection->unique('AttributeSize');
                $getattributesSize = $uniqueSize->values()->all();
                $getattributesColor = ProductConfiguration::select(
                        'product_configuration.product_id',
                        'product_configuration.AttributeColor',
                        'product_configuration.image as colorImage',
                        'attributes.*')
                        ->where('product_id', $product->id)
                        ->join('attributes', 'attributes.id', '=', 'product_configuration.AttributeColor')
                        ->get();
                $colorCollection = collect($getattributesColor);
                $uniqueColor = $colorCollection->unique('AttributeColor');
                $getattributesColor = $uniqueColor->values()->all();
                // $ratingReviews = RatingReviews::where('product_id',$product->id)->orderBy('created_at','DESC')->paginate(2);
                $ratingReviews =  $this->getReviewRatingList($product->id);
                $ratingAvg = RatingReviews::where('product_id', $product->id)->avg('rating');
                $ratingAvg = intval( ceil($ratingAvg) );
                $totalRatings = RatingReviews::where('product_id', $product->id)->count();
                $pConfiguration = ProductConfiguration::where('product_id', $product->id)->get();
                foreach ($pConfiguration as $key => $value) {
                    if($value['AttributeSize'] !== null)
                        $pConfiguration[$key]->AttributeSize = Attribute::select('name')->where('id', $value['AttributeSize'])->first();
                    if($value['AttributeColor']!== null)
                        $pConfiguration[$key]->AttributeColor = Attribute::select('name')->where('id', $value['AttributeColor'])->first();
                }
                //offers related data
                $offer = ProductsOffers::join("offers", "products_offers.offer_id", "=", "offers.id")
                    ->where("offers.is_active", "Y")
                    ->where("product_id", $product->id)
                    ->first();
                if ($offer !== null)
                    $productOffers = ProductsOffers::join("products", "products_offers.product_id", "=", "products.id")
                        ->where("offer_id", $offer->id)
                        ->whereNotIn("product_id", array($product->id))
                        ->groupBy("products.id")
                        ->select(
                            "products.id", 
                            "products.name",
                            "products.slug",
                            "products.image",
                            "products.sku",
                            "products.discount_price",
                            "products.price")
                        ->get();
                $brandName = Brand::where('id', $product->brand_id)->first();
                return view("user.product-details")
                    ->with(
                        compact(
                            'product',
                            'slug',
                            'productConfiguration',
                            'getattributesColor',
                            'getattributesSize' ,
                            'ratingReviews',
                            'pConfiguration',
                            'ratingAvg',
                            'totalRatings',
                            'flag',
                            'user',
                            'finalprice',
                            'brandName',
                            "offer",
                            "productOffers"
                        )
                    );
        }catch(\Exception $e){
            $data = [
                'input_params' => $slug,
                'action' => 'product details',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function displaySizeConfig(Request $request){
        try{
                $data = $request->all(); 
                $result="";
                $imgList= "";
                $ConfigurationImages = PColorImagesMap::where('product_id', $data['pid'])->where('color_id', $data['color'])->get();
                foreach ($ConfigurationImages as $key => $pConfig) {
                    if ($pConfig->config_img != null){
                        $imgList .= " <li class='configimgList changeImg' data-val='" . $pConfig->config_img . "' data-config='" . $pConfig->color_id . "'><a href='#'><img src='/uploads/products/images/" . $pConfig->product_id . "/80x85/" . $pConfig->config_img . "' alt='config-image'></a></li>";
                    }
                }
                $getattributesSize = ProductConfiguration::select('AttributeSize','quantity')
                    ->where('product_id' ,$data['pid'])
                    ->where('AttributeColor', $data['color'])
                    ->get();
                foreach ($getattributesSize as $key => $size) {
                    if ($size->quantity > 0){
                        $outOfStock = "";
                    } else {
                        $outOfStock = "outstockSize";   
                    }
                    if ($size->AttributeSize !== null) {
                        $sizeN = Attribute::select('name')->where('id', $size->AttributeSize)->first();
                        $result .= "<li class='sizeselect " . $outOfStock . "' data-val='" . $size->AttributeSize . "'>" . $sizeN->name . "</li>";
                    }
                }
                $finalData = [
                    'imgList' => $imgList,
                    'sizeList' => $result,
                  
                ];
                return $finalData;
            }catch(\Exception $e){
                $data = [
                    'input_params' => $data,
                    'action' => 'displaySizeConfig',
                    'exception' => $e->getMessage()
                ];
                Log::info(json_encode($data));
                abort(500);
            }
    }
    public function displayPriceConfig(Request $request){
        try{
                $data = $request->all();  
                $result="";
                $selectedSize= null;
                $selectedColor = null;
                $price = 0;
                $product = Product::where('id', $data['pid'])->first();
                if( $product['discount_price'] != null){
                        $discount_price = $product['discount_price'];
                }else{
                        $discount_price = 0;
                }
                if(isset($data['selectedSize']) && $data['selectedSize'] !== null){
                    $selectedSize = $data['selectedSize'];
                }
                if(isset($data['selectedColor']) && $data['selectedColor'] !== null){
                    $selectedColor = $data['selectedColor'];
                }
                $getprice = ProductConfiguration::select('price')
                    ->where('product_id', $data['pid'])
                    ->where('AttributeColor', $selectedColor)
                    ->where('AttributeSize', $selectedSize)
                    ->first();
                $price= intval($getprice['price']) - $discount_price;
                $price = number_format($price, 2);
                $result .="<span> ₹ " . $price . "</span>";
                return $result;
            }catch(\Exception $e){
                $data = [
                    'input_params' => $data,
                    'action' => 'displaySizeConfig',
                    'exception' => $e->getMessage()
                ];
                Log::info(json_encode($data));
                abort(500);
            }
    }

    public function displayImage(Request $request){
        try{
                $data = $request->all();
                $result = "";
                $result .= "<img src='/uploads/products/images/" . $data['pid'] . "/1024x1024/" . $data['imgName'] . "' alt='product'>";

                return $result;
            }catch(\Exception $e){
                $data = [
                    'input_params' => $request->all(),
                    'action' => 'displayImage',
                    'exception' => $e->getMessage()
                ];
                Log::info(json_encode($data));
                abort(500);
            }
    }


    public function addReview(Requests\ratingReviewRequest $request,$pid){
        try{
            $data = $request->all();
            $user= Auth::user();
            if($user === null){
                return redirect()->back()->with('error', 'To submit review & rating please login & continue.');
            }elseif( $data['email'] != $user->email_address){
                return redirect()->back()->with('error', 'Given email address is not matched.');
            }
            $arrData =[
                "product_id" => $pid,
                "name" => $data['name'],
                "email" => $data['email'],
                 "rating" => $data['rating'],
                 "message" => $data['message'],
                 "is_active"=>'N',
                 "created_at" => Carbon::now(),
            ];
            $resultPresent = RatingReviews::where('product_id', $pid)->where('email', $data['email'])->get();
            if(count($resultPresent) > 0){
                return redirect()->back()->with('error', 'You are already submitted Rating & Review for this product.');
            }else{
                $result = RatingReviews::insertGetId($arrData);
            }
            if($result > 0){
                return redirect()->back()->with('success', 'Rating & Review submitted successfully.');
            }else{
                return redirect()->back()->with('error', 'Rating & Review NOT submitted.Please try again');
            }
        }catch(\Exception $e){
                $data = [
                    'input_params' => $data,
                    'action' => 'addReview',
                    'exception' => $e->getMessage()
                ];
                Log::info(json_encode($data));
                abort(500);
        }
    }


    public function resultPrePage($result){
        try{
            session()->put("per_page", $result);
            return 1;
        }catch(\Exception $e){
            $data = [
                'input_params' => $result,
                'action' => 'resultPrePage',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function paginateReviewRatings(Request $request,$productID){
        try{
            $ratingReviews =  $this->getReviewRatingList($productID);
            $result = "";
            $pagination = $ratingReviews->links();
            $pagination ="$pagination";

            foreach ($ratingReviews as $review){
                $result .= "<li><h4>" . $review->name . "</h4><div class='rating'>";
                $result .= "<ul class='star-rating-name'>";
                    $i = 1;
                    for ($i = 1; $i <= 5; $i++) {
                        $selected = "";
                        if(!empty($review["rating"]) && $i <= $review["rating"]) {
                            $selected = "selected";
                        }
                
                        $result .= "<li class='" . $selected . "'>&#9733;</li>"; 
                    } 

                $result .= "<ul>";
                $result .= "</div><p>" . $review->message . "</p></li>";
            }
            $finalData = [
                'links'=>$pagination,
                'result'=>$result,
              
            ];
            return $finalData;
           
        }catch(\Exception $e){
            $data = [
                'input_params' => $productID,
                'action' => 'paginateReviewRatings',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
	public function metaCategoryPages(Request $request,$slug){
        try{
			$cmsPage= Category::where('slug', $slug)->first();
            if($cmsPage === null)
              abort(404);
            return view('layouts.user')->with(compact('cmsPage'));
        }catch(\Exception $e){
            $data = [
              'input_params' => $slug,
              'action' => 'Category page ',
              'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
}
