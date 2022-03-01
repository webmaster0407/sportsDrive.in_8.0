<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\CustomTraits\ProductTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Validator;
use DB;
use App\Http\Requests;
use App\Admin;
use App\CmsPage;
use App\Banner;
use Carbon\Carbon;
use App\Category;
use App\Product;
use App\Brand;
use App\ProductCategoryMap;
use App\Attribute;
use App\AttributeGroup;
use App\ProductConfiguration;
use App\ConfigImage;
use App\PCImagesMap;
use App\ProductFor;
use App\PColorImagesMap;
use App\ProductSlaveImage;
//use App\ProductConfigurationAttribute;

class ProductsController extends Controller
{
    public function __construct(){
       $this->middleware('adminauth');
    }

    use ProductTrait;
    public function listProducts(Request $request) { 
        try{
            $data = Product::orderby("updated_at", "DESC")->orderby("sort_order", "DESC")->get();
            return view('admin.list-products')->with('data', $data);
        }catch(\Exception $e){  
            $data = [
                'input_params' => $request,
                'action' => 'Admin list Products',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    //step1
    public function addProducts(){
        try{
            $data = array(
                "id"=>"",
                "name"=>"",
                "sku"=>"",
                "brand_id" =>"",
                "description"=>"",
                "meta_title" =>"",
                "meta_keyword" =>"",
                "meta_description"=>"",
                "short_description"=>"",
                 "product_specifications" =>"",
                "mode" => "add",
            );
            $data = (object) $data;
            $selectedMainCategories = array();
            $selectedSubCategories = array();
            $selectedSubSubCategories = array();
            $selectedProductUsedFor = array();
            $subCategories = array();
            $selectBrand = Brand::where('is_active','Y')->get();
            $mainCategories = $this->getAllCategories();
        return view('admin.add-product-step1')->with(compact('data','mainCategories','selectedMainCategories','selectedSubCategories','selectedSubSubCategories','selectedProductUsedFor','selectBrand'));
        }catch(\Exception $e){ dd($e->getMessage());
            $data = [
                'input_params' => null,
                'action' => 'Admin Add product',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

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
    public function addProductsData(Request $request){
        try{
            $rules = array(
                'name' => 'required|string',
                'brand' => 'required',
                'sku'=>'required|unique:products',
                "meta_title" =>'required',
                "meta_keyword" =>'required',
                "meta_description"=>'required',
                "short_description"=>'required',
                "product_specifications" => 'required',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
            $data = $request->all();
            $subCategory = array();
            $subSubCategory = array();
            $productUsedFor = array();
            $MainCategory = array();
            if(isset($data['mainCategories'])){
                $MainCategory = $data['mainCategories'];
            }
            if(isset($data['subCategories'])){
                  $subCategory = $data['subCategories'];
            }
            if(isset($data['subSubCategories'])){
                  $subSubCategory = $data['subSubCategories'];
            }
            if(isset($data['productUsedFor'])){
                  $productUsedFor = $data['productUsedFor'];
            }
            $slug =   $this->seo_friendly_url($data['name']);
            $slug = $this->checkduplicate_URLkey($slug);
             $maxSortOrder = Product::max('sort_order');
                $dataInsert = [
                    'name' =>  $data['name'],
                    'sku' =>  $data['sku'],
                    "brand_id" =>$data['brand'],
                    "description"=>$data['description'],
                    "meta_title" =>$data['meta_title'],
                    "meta_keyword" =>$data['meta_keyword'],
                    "meta_description"=>$data['meta_description'],
                    "short_description"=>$data['short_description'],
                    "product_specifications" => $data['product_specifications'],
                    'slug' => $slug,
                    'sort_order' =>  $maxSortOrder+1,
                    "completed_step" => 1,
                    'created_at' => Carbon::now(),
                ];
                $newId = $insert = Product::insertGetId($dataInsert);
                if(count($MainCategory)> 0){
                    foreach ($MainCategory as $value) {
                        $isPresent = ProductCategoryMap::where('category_id',$value)->where('product_id',$newId)->first();
                        if($isPresent == null){
                        ProductCategoryMap::insert(array('category_id' =>$value ,'product_id' =>$newId ));
                        }
                     }
                }
                if(count($subCategory)> 0){
                    foreach ($subCategory as $value1) {
                        $isPresent1 = ProductCategoryMap::where('category_id',$value1)->where('product_id',$newId)->first();
                        if($isPresent1 == null){
                        ProductCategoryMap::insert(array('category_id' =>$value1 ,'product_id' =>$newId ));
                        }
                     }
                }
                if(count($subSubCategory)> 0){
                    foreach ($subSubCategory as $value2) {
                        $isPresent2 = ProductCategoryMap::where('category_id',$value2)->where('product_id',$newId)->first();
                        if($isPresent2 == null){
                        ProductCategoryMap::insert(array('category_id' =>$value2 ,'product_id' =>$newId ));
                        }
                     }
                }
                 if(count($productUsedFor)> 0){
                    foreach ($productUsedFor as $usedFor) {
                        $isPresent = ProductFor::where('product_id',$newId)->where('for',$usedFor)->first();
                          if($isPresent == null){
                        ProductFor::insert(array('for' =>$usedFor ,'product_id' =>$newId,"created_at"=>Carbon::now()));
                        }
                      }
                }
                if ($insert > 0) {
                    return redirect('/administrator/edit-products/step2/'.$newId )->with('success', 'Completed first step successfully.');
                } else {
                    return redirect('/administrator/list-products')->with('error', 'Product not added successfully.');
                }
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request,
                'action' => 'Admin Add product',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }

    }
    public function editProducts($productsId){
        try{ 
            $productsdetails = Product::where('id',$productsId)->first();
            if($productsdetails != null){
                 $productsdetails->mode = 'edit';
            }
            $data = $productsdetails;

            $selectedMainCategories = Category::where("parent_id",0)
                ->where("level_id",0)
                ->join('product_category_map','categories.id','=','product_category_map.category_id')
                ->where("product_id",$productsId)
                ->pluck("categories.id")->toArray();

            $selectedSubCategories = Category::where("level_id",1)
                ->join('product_category_map','categories.id','=','product_category_map.category_id')
                ->where("product_id",$productsId)
                ->pluck("categories.id")->toArray();

            $selectedSubSubCategories = Category::where("level_id",2)
                ->join('product_category_map','categories.id','=','product_category_map.category_id')
                ->where("product_id",$productsId)
                ->pluck("categories.id")->toArray();

            $mainCategories = $this->getAllCategories();
            $selectedProductUsedFor = ProductFor::where("product_id",$productsId)->pluck("for")->toArray();
            $selectBrand = Brand::where('is_active','Y')->get();
            return view('admin.add-product-step1')->with(compact('data','mainCategories','selectedMainCategories','selectedSubCategories','selectedSubSubCategories','selectedProductUsedFor','selectBrand'));
         }catch(\Exception $e){
            $data = [
                'input_params' => $productsId,
                'action' => 'Admin Edit Product',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    
    public function updateProductsData(Request $request){
        try{
            $rules = array(
                'name' => 'required|string',
                'brand' => 'required',
                "meta_title" =>'required',
                "meta_keyword" =>'required',
                "meta_description"=>'required',
                "short_description"=>'required',
                "product_specifications" => 'required',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
            $data = $request->all();
            $id =$data['product_id'];
            $subCategory = array();
            $subSubCategory = array();
            $CategoryArrDelete =array();
            $CategoryArrAdd =array();
            $ProductArrAdd =array();
            $ProductArrDelete =  array();
            $ProductArrAdd = array();
            $productUsedFor = null;
            $MainCategory = array();
            //new categories
            if(isset($data['mainCategories'])){
                $MainCategory = $data['mainCategories'];
            }
            if(isset($data['subCategories'])){
                  $subCategory = $data['subCategories'];
            }
            if(isset($data['subSubCategories'])){
                  $subSubCategory = $data['subSubCategories'];
            }
            if(isset($data['productUsedFor'])){
                  $productUsedFor = $data['productUsedFor'];
            }

            //previous selected categories which is in db
            $selectedMainCategories =Category::select('categories.id')->where("parent_id",0)->where("level_id",0)->join('product_category_map','categories.id','=','product_category_map.category_id')->where("product_id",$id)->orderBy('sort_order','DESC')->get();
            $selectedSubCategories =Category::select('categories.id')->where("level_id",1)->join('product_category_map','categories.id','=','product_category_map.category_id')->where("product_id",$id)->orderBy('sort_order','DESC')->get();
            $selectedSubSubCategories =Category::select('categories.id')->where("level_id",2)->join('product_category_map','categories.id','=','product_category_map.category_id')->where("product_id",$id)->orderBy('sort_order','DESC')->get();
           $selectedProductUsedFor = ProductFor::where("product_id",$id)->pluck("for")->toArray();
                $dataInsert = [
                    'name' =>  $data['name'],
                    "brand_id" =>$data['brand'],
                    "description"=>$data['description'],
                    "meta_title" =>$data['meta_title'],
                    "meta_keyword" =>$data['meta_keyword'],
                    "meta_description"=>$data['meta_description'],
                    "short_description"=>$data['short_description'],
                    "product_specifications" => $data['product_specifications'],
                    'updated_at' => Carbon::now(),
                ]; 
               
                 $updateval = Product::where('id',$id)->update($dataInsert);  
                /* update enteries in product category map table
                 MainCategory subCategory subSubCategory */ 
                if(count($MainCategory)> 0){
                    //add new catgories & get delete ids
                    foreach ($MainCategory as $value) {
                        if($selectedMainCategories != null){
                            $collection = collect($selectedMainCategories);
                            $previousPresent = $collection->contains($value);
                            if($previousPresent == true){
                                $CategoryArrDelete[] = $value;
                                //old
                            }else{
                                $CategoryArrAdd[] = $value;
                                //new
                            }
                        }  
                    }                   
                    //to delete ids
                    $collection = collect($selectedMainCategories);
                    $diff = $collection->diff($CategoryArrDelete);
                    $deleteCatIds= $diff->all(); 

                    if(!empty($deleteCatIds) ){ 
                        foreach ($deleteCatIds as $key => $deleteval) {
                            ProductCategoryMap::where('category_id',$deleteval->id)->where('product_id',$id)->delete();
                        }
                    }
                    if(count($CategoryArrAdd)>0){
                        //to add ids 
                        foreach ($CategoryArrAdd as $key => $addVal) {
                             $isPresent = ProductCategoryMap::where('category_id',$addVal)->where('product_id',$id)->first();
                             if($isPresent == null){
                             ProductCategoryMap::insert(array('category_id' =>$addVal,'product_id' =>$id ));
                             }
                        }
                    }

                }
               
                if(count($subCategory)> 0){
                    //add new catgories & get delete ids
                    foreach ($subCategory as $value) {
                        if($selectedSubCategories != null){
                            $collection = collect($selectedSubCategories);
                            $previousPresent = $collection->contains($value);
                            if($previousPresent == true){
                                $CategoryArrDelete[] = $value;
                                //old
                            }else{
                                $CategoryArrAdd[] = $value;
                                //new
                            }
                        }  
                    }                   
                    //to delete ids
                    $collection = collect($selectedSubCategories);
                    $diff = $collection->diff($CategoryArrDelete);
                    $deleteCatIds= $diff->all();
                    if(!empty($deleteCatIds) ){ 
                        foreach ($deleteCatIds as $key => $deleteval) {
                            ProductCategoryMap::where('category_id',$deleteval->id)->where('product_id',$id)->delete();
                        }
                    }
                    if(count($CategoryArrAdd)>0){
                        //to add ids 
                        foreach ($CategoryArrAdd as $key => $addVal) {
                             $isPresent = ProductCategoryMap::where('category_id',$addVal)->where('product_id',$id)->first();
                             if($isPresent == null){
                             ProductCategoryMap::insert(array('category_id' =>$addVal,'product_id' =>$id ));
                             }
                        }
                    }
                }else{
                    
                    if($selectedSubCategories != null)
                    foreach ($selectedSubCategories as $key => $deleteval) {
                            ProductCategoryMap::where('category_id',$deleteval->id)->where('product_id',$id)->delete();
                        }
                }

                if(count($subSubCategory)> 0){
                    //add new catgories & get delete ids
                    foreach ($subSubCategory as $value) {
                        if($selectedSubSubCategories != null){
                            $collection = collect($selectedSubSubCategories);
                            $previousPresent = $collection->contains($value);
                            if($previousPresent == true){
                                $CategoryArrDelete[] = $value;
                                //old
                            }else{
                                $CategoryArrAdd[] = $value;
                                //new
                            }
                        }  
                    }                   
                    //to delete ids
                    $collection = collect($selectedSubSubCategories);
                    $diff = $collection->diff($CategoryArrDelete);
                    $deleteCatIds= $diff->all(); 

                    if(!empty($deleteCatIds) ){ 
                        foreach ($deleteCatIds as $key => $deleteval) {
                            ProductCategoryMap::where('category_id',$deleteval->id)->where('product_id',$id)->delete();
                        }
                    }
                 
                    if(count($CategoryArrAdd)>0){
                        //to add ids 
                        foreach ($CategoryArrAdd as $key => $addVal) {
                             $isPresent = ProductCategoryMap::where('category_id',$addVal)->where('product_id',$id)->first();
                             if($isPresent == null){
                             ProductCategoryMap::insert(array('category_id' =>$addVal,'product_id' =>$id ));
                             }
                        }
                    }

                }else{
                    
                    if($selectedSubSubCategories != null)
                    foreach ($selectedSubSubCategories as $key => $deleteval) {
                            ProductCategoryMap::where('category_id',$deleteval->id)->where('product_id',$id)->delete();
                        }
                }
               if(count($productUsedFor)> 0){
                    //add new catgories & get delete ids
                    foreach ($productUsedFor as $value) {
                        if($selectedProductUsedFor != null){
                            $collection = collect($selectedProductUsedFor);
                            $previousPresent = $collection->contains($value);
                            if($previousPresent == true){
                                $ProductArrDelete[] = $value;
                                //old
                            }else{
                                $ProductArrAdd[] = $value;
                                //new
                            }
                        }  
                    }
                    //to delete ids
                  $collection = collect($selectedProductUsedFor);
                    $diff = $collection->diff($ProductArrDelete);
                    $deleteProductIds= $diff->all();
                    if(!empty($deleteProductIds) ){

                        foreach ($deleteProductIds as $key => $deleteval) {

                            ProductFor::where('for',$deleteval)->delete();
                        }
                    }
                    if(count($ProductArrAdd)>0){
                        //to add ids 
                        foreach ($ProductArrAdd as $key => $addVal) {
                             $isPresent = ProductFor::where('product_id',$id)->where('for',$addVal)->first();
                             if($isPresent == null){
                             ProductFor::insert(array('for' =>$addVal,'product_id' =>$id ));
                             }
                        }
                    }
                }else{
                    if($selectedProductUsedFor != null)
                    foreach ($selectedProductUsedFor as $key => $deleteval) {
                            ProductFor::where('product_id',$deleteval->id)->delete();
                        }
                }
                if ($updateval > 0) {
                    return redirect('/administrator/edit-products/step2/'.$id )->with('success', 'updated first step successfully.');
                } else {
                    return redirect('/administrator/list-products')->with('error', 'Product not updated successfully.');
                }
            }
        }catch(\Exception $e){ dd( $e->getMessage());
            $data = [
                'input_params' => $request,
                'action' => 'Admin update product',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }

    }
    //step2
    public function addProductsStep2($id){
        try{
            $data = Product::where('id',$id)->first();
            $slaveImages = null;
            if($data != null){
                 $data->mode = 'edit';
            }

            $slaveImages = ProductSlaveImage::where('product_id',$id)->get();
        return view('admin.add-product-step2')->with(compact('data','slaveImages'));
        }catch(\Exception $e){  dd($e->getMessage());

            $data = [
                'input_params' => null,
                'action' => 'Admin Add product step 2',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function updateProductsDataStep2(Request $request){
        try{
            $rules = array(
                'price' => 'required',
                "size_chart_type"=>'required', 
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $data = $request->all();
                $id = $data['product_id'];
                $size_chart_image = '';
                $size_chart_description ='';
                $details = Product::where('id',$id)->first();
                if($data['size_chart_type'] == "image"){
                    if(($request->file('size_chart_image'))){
                        $photo = $request->file('size_chart_image');
                        $ds = DIRECTORY_SEPARATOR;
                        $imageName = uniqid()."".$request->file('size_chart_image')->getClientOriginalName();
                        $destinationPath =  public_path().$ds."uploads".$ds.'sizechart'.$ds.$id;
                        $resizeImagePath = $destinationPath.$ds."500x500";
                        if (!file_exists($resizeImagePath)) {
                                File::makeDirectory($resizeImagePath, $mode = 0777, true, true);
                        }
                        Image::make($photo)->resize(500,500)->save($resizeImagePath.$ds.$imageName);
                        Image::make($photo)->save($destinationPath.$ds.$imageName);

                        $size_chart_image = $imageName;
                        $size_chart_description ="";
                     } else {
                        $size_chart_image = $details['size_chart_image'];
                    }
                }else{
                    $size_chart_image = $details['size_chart_image'];
                }               
                if($data['size_chart_type'] == "desc"){
                    $size_chart_description = $data['size_chart_description'] ;
                    $size_chart_image = "";
                }else{
                    $size_chart_description = "" ;
                }
                if(($request->file('image'))){
                    $photo = $request->file('image');
                    $ds = DIRECTORY_SEPARATOR;
                    $mainimageName = uniqid()."".$request->file('image')->getClientOriginalName();
                    $destinationPath =  public_path().$ds."uploads".$ds.'products'.$ds.'images'.$ds.$id;

                    $resizeImagePath = $destinationPath.$ds."1024x1024";
                    if (!file_exists($resizeImagePath)) {
                            File::makeDirectory($resizeImagePath, $mode = 0777, true, true);
                    }
                    Image::make($photo)->resize(1024,1024)->save($resizeImagePath.$ds.$mainimageName);

                    /*$resizeImagePath2 = $destinationPath.$ds."217x217";
                    if (!file_exists($resizeImagePath2)) {
                            File::makeDirectory($resizeImagePath2, $mode = 0777, true, true);
                    }
                    Image::make($photo)->resize(217,217)->save($resizeImagePath2.$ds.$mainimageName);*/

                    $resizeImagePath4 = $destinationPath.$ds."250x250";
                    if (!file_exists($resizeImagePath4)) {
                            File::makeDirectory($resizeImagePath4, $mode = 0777, true, true);
                    }
                    Image::make($photo)->resize(250,250)->save($resizeImagePath4.$ds.$mainimageName);

                    $resizeImagePath3 = $destinationPath.$ds."80x85";
                    if (!file_exists($resizeImagePath3)) {
                            File::makeDirectory($resizeImagePath3, $mode = 0777, true, true);
                    }
                    Image::make($photo)->resize(80,85)->save($resizeImagePath3.$ds.$mainimageName);

                    $resizeImagePath5 = $destinationPath.$ds."500x500";
                    if (!file_exists($resizeImagePath5)) {
                        File::makeDirectory($resizeImagePath5, $mode = 0777, true, true);
                    }
                    Image::make($photo)->resize(500,500)->save($resizeImagePath5.$ds.$mainimageName);

                    Image::make($photo)->save($destinationPath.$ds.$mainimageName);

                 }else{
                    $mainimageName= $details['image'];
                 }

                  if(($request->file('pr_icon'))){
                    $pr_icon = $request->file('pr_icon');
                    $ds = DIRECTORY_SEPARATOR;
                    $pr_icon_name = uniqid()."".$request->file('pr_icon')->getClientOriginalName();
                    $destinationPath_icon =  public_path().$ds."uploads".$ds.'product_icon'.$ds.$id;
                    $resizeImagePath_icon = $destinationPath_icon.$ds."65x60";
                    if (!file_exists($resizeImagePath_icon)) {
                            File::makeDirectory($resizeImagePath_icon, $mode = 0777, true, true);
                    }
                    Image::make($pr_icon)->resize(65,60)->save($resizeImagePath_icon.$ds.$pr_icon_name);
                    $pr_icon->move($destinationPath_icon, $pr_icon_name);
                   // Image::make($pr_icon)->save($destinationPath_icon.$ds.$pr_icon_name);

                 }else{
                    $pr_icon_name= $details['icon'];
                 }

                $dataInsert = [
                    "price" =>$data['price'],
                    "gst" =>$data['gst'],
                    "hsn" =>$data['hsn'],
                    "size_chart_type"=>$data['size_chart_type'],
                    "size_chart_description"=>$size_chart_description,
                    "size_chart_image" => $size_chart_image,
                    "image" =>$mainimageName,
                    "icon"=>$pr_icon_name,
                    "video_url" =>$data['video_url'],
                    "completed_step" => 2,
                    'updated_at' => Carbon::now(),
                ];
                $updateval = Product::where('id',$id)->update($dataInsert);
                if ($updateval > 0) {
                    return redirect('/administrator/edit-products/step3/'.$id )->with('success', 'Completed second step successfully.');
                } else {
                    return redirect('/administrator/list-products')->with('error', 'Product not added successfully.');
                }
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request,
                'action' => 'Admin update product',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }

    }
    //step3
    public function addProductsStep3($id){
        try{

            $details = Product::where('id',$id)->first();
            if($details != null){
                 $details->mode = 'edit';
            }
            $data = $details;
            $attributeGroups = AttributeGroup::where('is_active','Y')->get();
            
            $productConfiguration =ProductConfiguration::where('product_id',$id)->orderBy('AttributeColor','ASC')->get();
            foreach ($productConfiguration as $key => $value) {
                if($value['AttributeSize']!= null){
                   
                    $productConfiguration[$key]->AttributeSizeName = Attribute::select('name')->where('id', $value['AttributeSize'])->first();}
                if($value['AttributeColor']!= null){
                    $Flag="Color";
                    $productConfiguration[$key]->AttributeColorName = Attribute::select('name')->where('id', $value['AttributeColor'])->first();}
            }
            //dd($productConfiguration);
        return view('admin.add-product-step3')->with(compact('data','attributeGroups','productConfiguration'));
        }catch(\Exception $e){
            $data = [
                'input_params' => $id,
                'action' => 'Admin Add product step 3',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function updateProductsDataStep3(Request $request){
        try{
            $rules = array(
                'quantity' => 'required',
                "discount_price" =>'required',                 
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $resultval =0;
                $data = $request->all();  
                $id = $data['product_id'];
                if(isset($data['attribute_group']))
                    $attribute_group = $data['attribute_group'];
                else
                    $attribute_group = null;
                $attribute_group_count = count($attribute_group);

                $config_group="Both";
                if($attribute_group_count == 1 && isset($data['AttributeColor'])){
                     $config_group="Color";
                }elseif($attribute_group_count == 1 && isset($data['AttributeSize'])){
                     $config_group="Size";
                }
                $configQuantity =0;
                $productConfigCount = ProductConfiguration::where('product_id',$id)->count();

                if(isset($data['AttributeColor']) ||isset($data['AttributeSize']) ){
                     $configQuantity = intval($data['configQuantity']) + $productConfigCount;
                 }else{
                    $configQuantity =$productConfigCount;
                 }
                $total =intval($data['quantity']);
                $AttributeColor=null;
                $AttributeSize=null;
                $price=null;
                $cQuantity=null;
                $configPresent = null;
                $sumQuantity = 0;
                
                $dataInsert = [   
                    "quantity" =>$data['quantity'],
                    "discount_price" =>$data['discount_price'],
                    "is_completed"=>'Y',
                    "completed_step" => 3,
                    "config_group"=>$config_group,
                    'updated_at' => Carbon::now(),
                ]; 
                //removed images w.r.t color
                if($config_group =="Size"){
                    PColorImagesMap::where('product_id',$id)->delete();
                    Product::where('product_id',$id)->update(array('image'=>null));
                }
               // product config 
                if( $configQuantity!= null && $configQuantity >0){ 
                    
                    for ($i=0; $i < $configQuantity; $i++) { 

                        $cQuantity= 0;
                        if(isset($data['AttributeColor']) && array_key_exists($i, $data['AttributeColor']))
                        $AttributeColor=$data['AttributeColor'][$i];

                        if(isset($data['AttributeSize'])&& array_key_exists($i, $data['AttributeSize']))
                        $AttributeSize=$data['AttributeSize'][$i];

                        if(isset($data['price'])&& array_key_exists($i, $data['price']))
                        $price=$data['price'][$i];
                        
                        if(isset($data['cQuantity'])&& array_key_exists($i, $data['cQuantity']))
                        $cQuantity=$data['cQuantity'][$i];

                        $sumQuantity += $cQuantity;

                        if($sumQuantity > $total){
                            return redirect()->back()->with('error', 'Configuration quantity Exceeds Total Quantity.Please try again.');
                        }
                        
                        $configDataArr = [
                        "product_id" => $id,
                        "AttributeColor" => $AttributeColor,
                        "AttributeSize" =>$AttributeSize,
                        "price" =>$price,
                        "quantity" =>$cQuantity,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                         ];

                        $configPresent = null;
                        if(isset($data['config_id']) && array_key_exists($i, $data['config_id'])) 
                        $configPresent =ProductConfiguration::where('id', $data['config_id'][$i])->first();
                       
                        if($configPresent == null)
                            $resultval = ProductConfiguration::insert($configDataArr);
                        else{
                            $resultval = ProductConfiguration::where('id',$configPresent['id'])->update($configDataArr);
                            //onupdate image mapping
                            PColorImagesMap::where('product_id', $id)->where('color_id', $configPresent['AttributeColor'])->update(array('color_id'=>$AttributeColor));
                        }

                    }
                    
                }
                //end product config
                $updateval = Product::where('id',$id)->update($dataInsert);  
                if ($resultval > 0 || $updateval >0) {
                    return redirect('/administrator/edit-products/step3/'.$id )->with('success', 'Configurations added successfully.');
                } else {
                    return redirect('/administrator/list-products')->with('error', 'Product not added successfully.');
                }
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'Admin update product',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    
    public function changeStatusProducts(Request $request) {
        try{
            $data = $request->all();

            $operationFlag = $data['operationFlag'];
            $pID = $data['chk'];
            $updateVal = 0;
            $message = "Something went wrong!Please try again";
            if ($operationFlag == 'active') {
                $updateVal = Product::whereIn('id', $pID)->update(array('is_active' => 'Y'));
                $message = "Product/s successfully activated.";
            } else if ($operationFlag == 'deactive') {
                $updateVal = Product::whereIn('id', $pID)->update(array('is_active' => 'N'));
                $message = "Product/s successfully deactivated.";
            } else if ($operationFlag == 'delete') {
                $updateVal = Product::whereIn('id', $pID)->delete();
                $message = "Product/s successfully deleted.";
            }else if ($operationFlag == 'setFeatured') {
                $updateVal = Product::whereIn('id', $pID)->update(array('is_featured' => 'Y'));
                $message = "Product/s successfully added to Featured.";
            } else if ($operationFlag == 'unsetFeatured') {
                $updateVal = Product::whereIn('id', $pID)->update(array('is_featured' => 'N'));
                $message = "Product/s successfully removed from Featured.";
            }else if ($operationFlag == 'setNew') {
                $updateVal = Product::whereIn('id', $pID)->update(array('is_new' => 'Y'));
                $message = "Product/s successfully set as new.";
            } else if ($operationFlag == 'unsetNew') {
                $updateVal = Product::whereIn('id', $pID)->update(array('is_new' => 'N'));
                $message = "Product/s successfully unset from new.";
            }

            if ($updateVal > 0) {
                return redirect("/administrator/list-products")->with('success', $message);
            } else {
                return redirect("/administrator/list-products")->with('error', $message);
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'change Status of products ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function order_down($id,$order){
        try{
            $id = base64_decode($id);
            $nextorder = $order+1;
            $updateval=0;
            $updateval =Product::where('sort_order', $nextorder)->decrement('sort_order');
            $updateval = Product::where('id',$id)->increment('sort_order');
            
            if ($updateval == 1) {
                return redirect("/administrator/list-products")->with('success', 'Order change successfully.');
            } else {
                return redirect("/administrator/list-products")->with('error', 'Order not change successfully.');
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $id,
                'action' => 'change order down of products ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function order_up($id,$order){
        try{ 
            $id = base64_decode($id);
            $updateval=0;
            $nextorder = $order-1;
           
            $updateval = Product::where('sort_order', $nextorder)->increment('sort_order');
            $updateval = Product::where('id', $id)->decrement('sort_order');
           
            if ($updateval == 1) {
                return redirect("/administrator/list-products")->with('success', 'Order change successfully.');
            } else {
                return redirect("/administrator/list-products")->with('error', 'Order not change successfully.');
            }  
        }catch(\Exception $e){ dd($e->getMessage());
            $data = [
                'input_params' => $id,
                'action' => 'change order up of products ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        } 
    }
    public function getSubCategory(Request $request) {
        $data = $request->all();
        $catID = $data['catID'];
        $option = "";

        for($i= 0; $i < (count($catID)); $i++){
            $category[$i] = Category::select('id', 'name', 'level_id')
                            ->where([["parent_id", $catID[$i]], ["is_active", "Y"]])
                            ->get();
            if(count($category[$i])>0){
                $categoryDetails = Category::select('id', 'name', 'level_id')->where("id",$catID[$i])->first();
                $option .= "<option  disabled>".$categoryDetails->name."</option>";
                foreach ($category[$i] as $key => $item) {
                    $option .= "<option  value='".$item->id."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-".$item->name."</option>";
                }
            }
        }
        return $option;
    }

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
    function seo_friendly_url($string) {
        $string = str_replace(array('[\', \']'), '', $string);
        $string = preg_replace('/\[.*\]/U', '', $string);
        $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
        $string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string);
        $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/'), '-', $string);
        return strtolower(trim($string, '-'));
    }
    
    public function checkduplicate_URLkey($urlKey) {

        $checkExist = Product::where('slug', $urlKey)
                        ->select("id")->count();
        if ($checkExist == 0) {
            return $urlKey;
        }
        $i = 1;
        while ($checkExist > 0) {
            $urlKeyNw = $urlKey . '_' . $i;
            $checkExist = Product::where('slug', $urlKeyNw)
                            ->select("id")->count();

            if ($checkExist == 0) {
                return $urlKeyNw;
            }
            $i++;
        }
    }

    public function removeCategory(Request $request){
        $data = $request->all();
            $cName = $data['cName'];
            $pID = $data['pID'];
        $category = Category::where('name',$cName)->first();   
        if($category!= null){
            $record = ProductCategoryMap::where('product_id',$pID)->where('category_id',$category['id'])->first();
            if($record!= null){
               $result = $record->delete();
                return  $result;
            }
        }
        return  0;
    }

    public function getAttribute(Request $request){
        $data = $request->all(); 
        $prodID = $data['prodID'];
        $mode = $data['mode'];
        $attributeGrp = $data['attributeGrp'];
        $tblCol="";
        $tblColVal ="";
        $attributeOptions ="";
        $temp = array();
        $result = "";
        $count =0;
        $countNew =$data['countNew'];
        $product = Product::where('id',$prodID)->first();
        //product configurations
        $productConfiguration =ProductConfiguration::where('product_id',$prodID)->orderBy('created_at','DESC')->get();
        $count =count($productConfiguration);
        
        //for new row attributes
        for ($i=0; $i <count($attributeGrp) ; $i++) { 
            $tblColVal ="";
            $attributeOptions = "";
            $AttributeGroup = AttributeGroup::where('id',$attributeGrp[$i])->where('is_active','Y')->first();

            $tblCol.="<th>".$AttributeGroup['name']."</th>";

            $attributes = Attribute::where('group_id',$attributeGrp[$i])->where('is_active','Y')->get();
            if($attributes != null){
                foreach ($attributes as $attribute) {
                 
                    $attributeOptions .="<option  value='".$attribute['id']."'>".$attribute['name']."</option>";
                }
            }else{
                 $attributeOptions .="<option value=''>NA</option>";
            }

            $tblColVal .="<td><select id='Attribute' name='Attribute".$AttributeGroup['name']."[]'>".$attributeOptions."</select></td>";
            $temp[$attributeGrp[$i]] =  $tblColVal ;
        }
        //end
          
            $result .= " <div class='newConfig'><label class='col-sm-3 control-label'> Configurations </label><div class='col-sm-8' style='overflow-y:scroll;'>";
            $result .= "<table id='configtable' class='table table-bordered table-striped col-sm-8'><thead><tr><th>Delete<input type='checkbox' id='checkAll' name='chkAll' value='checkbox'></th>";
            
            $result .= $tblCol;
            $result .="<th>price</th><th>quantity</th>";
            if($count >0 && (count($attributeGrp) == 2 ||(count($attributeGrp) == 1 && $attributeGrp[0] != "2"))){
            $result .="<th>image</th>";
            }
            $result .="</tr></thead><tbody>";
        //previous config rows
          
            for ($j=0; $j <$count; $j++) {  
 
                $result .="<tr id='".$productConfiguration[$j]->id."' class='variationdiv preConfig'><td><input type='hidden' name='config_id[]' value='".$productConfiguration[$j]->id."' /><div class='i-checks'><label><input  type='checkbox'  class='checkBoxClass' name='deteleCheck' id='deteleCheck".$productConfiguration[$j]->id."' value='". $productConfiguration[$j]->id."' class='text' onclick='deleteFromDB(this)' /></label></div></td>";

                
                $colorAttributes = Attribute::where('group_id',1)->where('is_active','Y')->get();
                $attributeColorOptions ="";
              
                if($colorAttributes != null){
                    foreach ($colorAttributes as $attribute) {
                        if($productConfiguration[$j]->AttributeColor == $attribute['id']){
                            $selected ="selected";
                        }else{
                            $selected="";
                        }
                        
                            $attributeColorOptions .="<option ".$selected."  value='".$attribute['id']."'>".$attribute['name']."</option>";
                    }
                }else{
                     $attributeColorOptions .="<option value=''>NA</option>";
                }

                $sizeAttributes = Attribute::where('group_id',2)->where('is_active','Y')->get();
                $attributeSizeOptions ="";
                
                if($sizeAttributes != null){
                    foreach ($sizeAttributes as $attribute) {
                         if($productConfiguration[$j]->AttributeSize == $attribute['id']){
                            $selected ="selected";
                        }else{
                            $selected="";
                        }
                      
                        $attributeSizeOptions .="<option ".$selected."  value='".$attribute['id']."'>".$attribute['name']."</option>";                       
                    }
                }else{
                     $attributeSizeOptions .="<option value=''>NA</option>";
                }


                if(count($attributeGrp) == 2){
                    
                    $result .="<td><select id='AttributeColor' class='AttributeColor' name='AttributeColor[]'>".$attributeColorOptions."</select></td>";
                    $result .="<td><select id='AttributeSize' class='AttributeSize' name='AttributeSize[]'>".$attributeSizeOptions."</select></td>";
                     
                }elseif(count($attributeGrp) == 1 && $attributeGrp[0] == "1"){
                     $result .="<td><select id='AttributeColor' class='AttributeColor' name='AttributeColor[]'>".$attributeColorOptions."</select></td>";
                    

                }elseif(count($attributeGrp) == 1 && $attributeGrp[0] == "2"){
                   $result .="<td><select id='AttributeSize' class='AttributeSize' name='AttributeSize[]'>".$attributeSizeOptions."</select></td>";
                    
                }
             
                $result .="<td><input type='text' name='price[]' value='".$productConfiguration[$j]->price."' ></td><td><input type='text' name='cQuantity[]' value='".$productConfiguration[$j]->quantity."' >";

                if(count($attributeGrp) == 2||(count($attributeGrp) == 1 && $attributeGrp[0] != "2")){
                    $addImage = "addImages(".$productConfiguration[$j]->id.",".$productConfiguration[$j]->AttributeColor.")";
                    $result .="<td><input type='button' id='addImage-".$productConfiguration[$j]->id."' name='addImage' value='Add/View Images' onclick='".$addImage."'></td>";
                }
               
                $result .="</tr>";
            }
            // new rows
            for ($k=0; $k <$countNew; $k++) { 
                $result .="<tr id='0' class='variationdiv'><td><div class='i-checks'><label><input type='checkbox' name='deteleCheck' id='deteleCheck".$k."' value='". $k."' class='text' onclick='removeConfigDiv(this)' /></label></div></td>";
                

                for ($i=0; $i <count($attributeGrp) ; $i++) { 
                 $result .=  $temp[$attributeGrp[$i]];
                }
                $result .="<td><input type='text' name='price[]' ></td><td><input type='text' name='cQuantity[]' >";

               
                $result .="</tr>";
            }
            //new row end
            $result .= "</tbody></table></div>";
            $result .="</div>";
        

        return $result;
    }

    public function deleteAttribute(Request $request){
        $data = $request->all(); 
        $rId = $data['rId'];
            $configInfo = ProductConfiguration::where('id',$rId)->first(); 
            ProductConfiguration::where('id',$rId)->delete();
            //also remove from product_configuration_images_map
            PColorImagesMap::where('product_id', $configInfo['product_id'])->where('color_id', $configInfo['AttributeColor'])->delete();
        $result = "#".$rId;
        return $result;
    }

    public function addImages(Request $request,$configId){
        try{
            $data= $request->all();
            $configImages =PColorImagesMap::where('color_id',$data['colorId'])->where('product_id',$data['pid'])->orderby("sort_order", "ASC")->orderby("is_main", "DESC")->get();
            $mainImageselected =productConfiguration::where('AttributeColor',$data['colorId'])->where('product_id',$data['pid'])->first();
            $result = "";
            $result .="<div class='row uploaded-images' id='productIMG'>";
            if(count($configImages)>0){
                foreach($configImages as $configImage){
                    if($mainImageselected != null && $mainImageselected['image'] == $configImage['config_img']){
                        $selectStyle = 'selectStyle';
                        $checkIcon = "<i class='fa fa-check'></i>";
                    }else{
                        $selectStyle = '';
                        $checkIcon = '';
                    }

                    $result .="<div class='col-sm-3 imgWrapGallery' id='".$configImage['id']."'>";
                    $result .=" <img  src='/uploads/products/images/".$configImage->product_id."/80x85/".$configImage['config_img']."' class='img-thumbnail ".$selectStyle."'  width='120'/>";
                    $result .="<div class='checkbox'><a href='#' onclick=deleteImages('$configImage->id') title='Delete'><label style='color:red;'>Delete</label></a>
					<a href='#' class='setMainConfig' onclick=setImages('$configImage->id','$configImage->color_id') title='Set as Main'>
						<label>Set as Main ".$checkIcon.'</label></a>
                    </div>';
                    $result .="</div>";
                }


            }
            $result .="</div>";
            return $result;
        }catch(\Exception $e){ dd($e->getMessage());
            $data = [
                'input_params' => $request->all(),
                'action' => 'add images',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function uploadImages(Request $request){
        try{
            $data = $request->all(); //dd($data);
            $colorId =  $data['color_id'];
            $product_id =$data['product_id'];
            $returnImage ="";
            if(($request->hasFile('file'))){
                $file = $request->file('file');//dd($file);
                $ds = DIRECTORY_SEPARATOR;
                $ImageName= rand(1000,9999999).$file->getClientOriginalName();
                $ImageUploadPath = public_path().$ds."uploads".$ds.'/products/images/'.$ds.$product_id;

                /* Create Upload & Resize Directory If Not Exists */
                $resizeImagePath = $ImageUploadPath.$ds."1024x1024";
                if (!file_exists($resizeImagePath)) {
                    File::makeDirectory($resizeImagePath, $mode = 0777, true, true);
                }
                Image::make($file)->resize(1024,1024)->save($resizeImagePath.$ds.$ImageName);

                $resizeImagePath2 = $ImageUploadPath.$ds."80x85";
                if (!file_exists($resizeImagePath2)) {
                    File::makeDirectory($resizeImagePath2, $mode = 0777, true, true);
                }
                Image::make($file)->resize(80,85)->save($resizeImagePath2.$ds.$ImageName);

                /*$resizeImagePath3 = $ImageUploadPath.$ds."217x217";
                if (!file_exists($resizeImagePath3)) {
                    File::makeDirectory($resizeImagePath3, $mode = 0777, true, true);
                }
                Image::make($file)->resize(217,217)->save($resizeImagePath3.$ds.$ImageName);*/

                $resizeImagePath4 = $ImageUploadPath.$ds."250x250";
                if (!file_exists($resizeImagePath4)) {
                    File::makeDirectory($resizeImagePath4, $mode = 0777, true, true);
                }
                Image::make($file)->resize(250,250)->save($resizeImagePath4.$ds.$ImageName);

                $resizeImagePath5 = $ImageUploadPath.$ds."500x500";
                if (!file_exists($resizeImagePath5)) {
                    File::makeDirectory($resizeImagePath5, $mode = 0777, true, true);
                }
                Image::make($file)->resize(500,500)->save($resizeImagePath5.$ds.$ImageName);

                /*original image upload 11 sept*/
                $originalImagePath = $ImageUploadPath;
                Image::make($file)->save($originalImagePath.$ds.$ImageName);
                $arrData2 = [
                    "color_id" => $colorId,
                    "product_id" =>$product_id,
                    "config_img" => $ImageName,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $id = PColorImagesMap::insertGetId($arrData2);
            }
            $returnImage .="<div class='col-sm-3 imgWrapGallery' id='".$id."'>";
            $returnImage .="<img src='/uploads/products/images/".$product_id."/80x85/".$ImageName."' class='img-thumbnail'  width='120'>";
            $returnImage .="<div class='checkBox'><a href='' onclick=deleteImages('$id') title='Delete'><label style='color:red;'>Delete</label></a><br><a href='#' onclick=setImages('$id','$colorId') class='setMainConfig' title='Set as Main'><label>Set as Main</label></a>";
            $returnImage .="</div></div>";
            if ($request->ajax()) {
                return response()->json([
                    'appeendImg' => $returnImage,
                    'flag' => 'success',
                    'type' =>'configImage',
                ]);
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $colorId,
                'action' => 'upload images',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function deleteImages($imgId){
        try{
            $configImage =PColorImagesMap::where('id',$imgId)->first(); 
            $imgName =$configImage->config_img;

            $result = $this->removeImagesFromStorage($configImage->product_id,$imgName);
            $configImagesIds =PColorImagesMap::where('id',$imgId)->delete();
            $updateval =ProductConfiguration::where('image',$imgName)->update(array('image'=>null));
            return $imgId;

        }catch(\Exception $e){
            $data = [
                'input_params' => $imgId,
                'action' => 'delete images',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }

    }

    public function setImage(Request $request){
        try{
            $data= $request->all();
            $updateval = 0;
            $image = PColorImagesMap::where("id",$data['imageID'])->first();
            if($image){
                $updateval = ProductConfiguration::where('product_id',$data['pid'])->where('AttributeColor',$data['colorId'])->update(array('image'=>$image->config_img));
            }
            // get colorid & pid
            $previousMainImage =PColorImagesMap::where('product_id',$data['pid'])->where('color_id',$data['colorId'])->where('is_main','Y')->update(array('is_main'=>'N'));
            PColorImagesMap::where('id',$data['imageID'])->update(array('is_main'=>'Y'));
            return $updateval;
        }catch(\Exception $e){ dd($e->getMessage());
            $data = [
                'input_params' =>$data,
                'action' => 'set images',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    
}
