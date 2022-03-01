 <?php
 use App\CmsPage;
 
 function cmsHeaderMenu(){
     $headerMenu = CmsPage::where("is_header","Y")->select("page_title","slug")->orderby("sort_order")->get();
     return $headerMenu;
 }
 function getCurrentUrl(){
 	$temp = \Illuminate\Support\Facades\Request::path();
 	return $temp;
 }
 function getCategories(){
     $categories = \App\Category::where("is_active","Y")->where('is_header','Y')->where('level_id',0)->orderby("sort_order")->get()->toArray();
     foreach ($categories as $key=>$category){//get sub categories
         $subSubCount = 0;
         $subCategories = \App\Category::where("is_active","Y")->where('is_header','Y')->where('level_id',1)->where('parent_id',$category['id'])->orderby("sort_order")->get()->toArray();
         $categories[$key]['sub_categories'] = $subCategories;
         foreach($subCategories as $subCatKey => $subCategory){
             $subSubCategories = \App\Category::where("is_active","Y")->where('is_header','Y')->where('level_id',2)->where('parent_id',$subCategory['id'])->orderby("sort_order")->get()->toArray();
             $categories[$key]['sub_categories'][$subCatKey]['subSubCategories'] = $subSubCategories;
             $subSubCount =    $subSubCount+count($subSubCategories);
         }
         $categories[$key]['sub_sub_categories_count'] = $subSubCount;
     }
     return $categories;
 }

 function servicePages(){
     $admin = \App\Admin::first();
     $footerPages = CmsPage::where("is_active","Y")->where('is_footer','Y')->get();
     $servicePages = CmsPage::where("is_active","Y")->where('is_footer','Y')->where('slug','customer-services')->first();
    $data = [
        'admin'=>$admin,
        'footerPages'=>$footerPages,
        'servicePages'=>$servicePages,
    ];
    return $data;
 }

 function getCartCount(){
     $user = \Illuminate\Support\Facades\Auth::user();
     $cartCount = 0;
     $order_id = session()->get("order_id");
     if($user){
         $order = \App\Order::where("customer_id",$user->id)->where("is_completed","N")->first();
         if($order!=null)
          $cartCount = \App\Cart::where("order_id",$order->id)->where("is_deleted","N")->sum("quantity");
     }elseif(!empty($order_id)){
         $cartCount = \App\Cart::where("order_id",$order_id)->where("is_deleted","N")->sum("quantity");
     }
     return $cartCount;
 }