<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Validator;
use DB;
use App\Http\Requests;
use App\Admin;
use App\CmsPage;
use Carbon\Carbon;
use App\Category;
use App\Product;
use App\Brand;
use App\ProductCategoryMap;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('adminauth');
       // $this->middleware('auth');
    }

    public function listCategories(Request $request,$pid=null,$lid=null) {
        try{
            $parentId = 0;
            $levelId = 0;
            $linkPid = 0;
            $linkLid = 0;
             $ParentData = null;
             $SuperParentData = null;
            if($pid != null){
               $parentId = $pid;
               if($parentId == 0){
                 $levelId = 0;
               }
               else{
                    $level = Category::where("id", $parentId)->pluck('level_id');
                    $levelId = intval($level[0]) + 1;
                }
            }
            if($lid != null){
                $levelId = $lid;
            }
            //parent data
            if($parentId != 0){
                $ParentData = Category::where("id", $parentId)->first();
                $linkPid = $ParentData->parent_id;
                $linkLid = $ParentData->level_id;
            }
            //super parent data
            if($linkPid !=0){
                $SuperParentData = Category::where("id", $linkPid)->first();
            }
            $data = Category::where("parent_id", $parentId)->orderBy('sort_order','ASC')->get();
            //count of subcategories
            foreach ($data as $key => $value) {
               $count[$value['id']] = Category::where('parent_id', $value['id'])->count();
                $data[$key]->productCount = ProductCategoryMap::where("category_id",$value['id'])->count();
            }
            $data->parent_id = $parentId;
            $data->level_id = $levelId;
            return view('admin.list-categories')->with(compact('data','count','linkLid','linkPid','ParentData','SuperParentData'));
        }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => $request,
                'action' => 'Admin list Categories',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function addCategories($pid=null) {
        try{
        $parentId = 0;
            $levelId = 0;
            if($pid != null){
               $parentId = $pid;
               if($parentId == 0){
                 $levelId = 0;
               }
               else{
                    $level = Category::where("id", $parentId)->pluck('level_id');
                    $levelId = intval($level[0]) + 1;
                }
            }
        $order = 1;
        $sort_order = Category::max('sort_order');
        if( $sort_order != null){
            $sort_order = Category::where('level_id',$levelId)->max('sort_order');
           // dd($sort_order);
            $order =$sort_order + 1;
        }

        $data = array(
            "parent_id"=>$parentId,
            "level_id" =>$levelId,
            "id" => "",
            "name" => "",
            "slug" =>"",
            "meta_title" => "",
            "meta_keyword" => "",
			"meta_desc" => "",
            "short_description" => "",
            "description" => "",
            "sort_order" => $order,
            "mode" => "add",
            "image"=>"",
        );
        $object1 = (object) $data;
        return view('admin.add-category')->with('data', $object1);
        }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => Null,
                'action' => 'Admin Add  Categories',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function editCategory($categoryid) {
        try{
            $categorydetails = Category::where('id', $categoryid)->first();
            if($categorydetails!= null){
                $categorydetails->mode = 'edit';
            }
            return view('admin.add-category')->with('data', $categorydetails);
         }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => Null,
                'action' => 'Admin Edit  Categories',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function addCategoryData(Request $request) {
        try{
        $rules = array(
            'name' => 'required|string|min:3|max:50',
            'meta_title' => 'required|string|min:3|max:50',
            'meta_keyword' => 'required|string|min:3|max:50',
			'meta_desc' => 'required|string|min:3|max:50',
            'image' => 'image|mimes:jpeg,png,jpg|max:2048',
           
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            //426x210
            if(($request->file('image'))){
                 $photo = $request->file('image');
                 $ds = DIRECTORY_SEPARATOR;
                 $imageName =uniqid()."".$request->file('image')->getClientOriginalName();
                 $destinationPath =  public_path().$ds."uploads".$ds.'categories';

                $resizeImagePath = $destinationPath.$ds."426x210";
                if (!file_exists($resizeImagePath)) {
                        File::makeDirectory($resizeImagePath, $mode = 0777, true, true);
                }
                
                Image::make($photo)->resize(ENV('CATEGORY_W'),ENV('CATEGORY_H'))->save($resizeImagePath.$ds.$imageName);
                Image::make($photo)->save($destinationPath.$ds.$imageName);
                }else{
                  $imageName="";
                }

          $slug =   $this->seo_friendly_url(Input::get('name'));
          $slug = $this->checkduplicate_URLkey($slug);
            $parentId =Input::get('parent_id');
            $levelId = Input::get('level_id');
            $maxSortOrder = Category::where('level_id',$levelId)->where('parent_id',$parentId)->max('sort_order');
            $dataInsert = array(
                'name' => Input::get('name'),
                'slug' => $slug,
                'meta_title' => Input::get('meta_title'),
                'meta_keyword' => Input::get('meta_keyword'),
				'meta_desc' => Input::get('meta_desc'),
                'short_description' => Input::get('short_description'),
                'description' => Input::get('description'),
                'sort_order' => $maxSortOrder+1,
                'parent_id'=> $parentId,
                'level_id'=>$levelId,
                'image' =>$imageName,
                'created_at'=>Carbon::now(),
            );

            $insert = Category::insert($dataInsert);
            if ($insert == 1) {
                return redirect('/administrator/list-categories/'.$parentId.'/'.$levelId)->with('success', 'New category added successfully.');
            } else {
                return redirect('/administrator/list-categories/'.$parentId.'/'.$levelId)->with('error', 'New category not added successfully.');
            }
        }
        }catch(\Exception $e){ dd($e->getMessage());
            $data = [
                'input_params' =>Input::all(),
                'action' => 'add data  ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function updateCategoryData(Request $request) {
        try{
            //dd($request->all());
            $categoryID = Input::get('category_id');
            $data  =Category::where('id', $categoryID)->first();
            $rules = array(
                'name' => 'string',
                'meta_title' => 'string',
                'meta_keyword' => 'string',
				'meta_desc' => 'string',
				
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                if(($request->file('image'))){
                 $photo = $request->file('image');
                 $ds = DIRECTORY_SEPARATOR;
                 $imageName = uniqid()."".$request->file('image')->getClientOriginalName();
                 $destinationPath =  public_path().$ds."uploads".$ds.'categories';

                $resizeImagePath = $destinationPath.$ds."426x210";
                if (!file_exists($resizeImagePath)) {
                        File::makeDirectory($resizeImagePath, $mode = 0777, true, true);
                }
                
                Image::make($photo)->resize(ENV('CATEGORY_W'),ENV('CATEGORY_H'))->save($resizeImagePath.$ds.$imageName);
                Image::make($photo)->save($destinationPath.$ds.$imageName);
                }else{
                  $imageName=$data['image'];
                }
                $dataUpdate = array(
                    'name' => Input::get('name'),
                    'meta_title' => Input::get('meta_title'),
                    'meta_keyword' => Input::get('meta_keyword'),
					'meta_desc' => Input::get('meta_desc'),
                    'short_description' => Input::get('short_description'),
                    'description' => Input::get('description'),
                    'image' =>$imageName,
                    'updated_at'=>Carbon::now(),
                );
                $updateval =Category::where('id', $categoryID)->update($dataUpdate);
                if ($updateval == 1) {
                    return redirect('/administrator/list-categories/'.$data->parent_id.'/'.$data->level_id)->with('success', 'category updated successfully.');
                } else {
                    return redirect('/administrator/list-categories/'.$data->parent_id.'/'.$data->level_id)->with('error', 'Nothing to update.');
                }
            }
        }catch(\Exception $e){ dd($e->getMessage());
            $data = [
                'input_params' => $request->all(),
                'action' => 'update data  ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function changeStatusCategory(Request $request) {
        try{
            $data = $request->all();
            $operationFlag = $data['operationFlag'];
          
            $categoryID = $data['chk']; 
            $updateVal = 0;
            $topCount = 0;
            $message = "Something went wrong!Please try again";

            $topCount = Category::where('is_top','Y')->count();
            $bottomCount = Category::where('is_bottom','Y')->count();

            if ($operationFlag == 'active') {
                $updateVal = Category::whereIn('id', $categoryID)->update(array('is_active' => 'Y'));
                $message = "Category/s successfully activated.";
            } else if ($operationFlag == 'deactive') {
                $updateVal = Category::whereIn('id', $categoryID)->update(array('is_active' => 'N'));
                $message = "Category/s successfully deactivated.";
            } else if ($operationFlag == 'delete') {
                $updateVal = Category::whereIn('id', $categoryID)->delete();
                    //delete from mapping table
                    ProductCategoryMap::whereIn('category_id', $categoryID)->delete();
                $message = "Category/s successfully deleted.";
            } else if ($operationFlag == 'setHeader') {
                $updateVal = Category::whereIn('id', $categoryID)->update(array('is_header' => 'Y'));
                $message = "Category/s successfully added to header menu.";
            } else if ($operationFlag == 'unsetHeader') {
                $updateVal = Category::whereIn('id', $categoryID)->update(array('is_header' => 'N'));
                $message = "Category/s successfully removed from header menu.";
            }else if ($operationFlag == 'setTop' && $topCount < 6) {
                $updateVal = Category::whereIn('id', $categoryID)->update(array('is_top' => 'Y'));
                $message = "Category/s successfully added to top-Categories.";
            } else if ($operationFlag == 'unsetTop') {
                $updateVal = Category::whereIn('id', $categoryID)->update(array('is_top' => 'N'));
                $message = "Category/s successfully removed from top-Categories.";
            }
            else if ($operationFlag == 'setBottom' && $bottomCount < 6) {
                $updateVal = Category::whereIn('id', $categoryID)->update(array('is_bottom' => 'Y'));
                $message = "Category/s successfully added to Bottom-Categories.";
            } else if ($operationFlag == 'unsetBottom') {
                $updateVal = Category::whereIn('id', $categoryID)->update(array('is_bottom' => 'N'));
                $message = "Category/s successfully removed from Bottom-Categories.";
            }
            
            if(!isset($data['level_id']))
                $data['level_id'] = 0;
            
          
            if ($updateVal > 0) {
                return redirect("/administrator/list-categories/".$data['parent_id'].'/'.$data['level_id'])->with('success', $message);
            } else {
                return redirect("/administrator/list-categories/".$data['parent_id'].'/'.$data['level_id'])->with('error', $message);
            }
        }catch(\Exception $e){ dd($e->getMessage());
            $data = [
                'input_params' => $request->all(),
                'action' => 'change Status of Category ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

     public function order_down($id,$order,$level,$parent){
        try{
            $id = base64_decode($id);
            $nextorder = $order+1;
            $updateval=0;
            $updateval =Category::where('level_id', $level)->where('parent_id', $parent)->where('sort_order', $nextorder)->decrement('sort_order');
            $updateval = Category::where('id',$id)->increment('sort_order');
            
            if ($updateval == 1) {
                return redirect("/administrator/list-categories/".$parent."/".$level)->with('success', 'Order change successfully.');
            } else {
                return redirect("/administrator/list-categories/".$parent."/".$level)->with('error', 'Order not change successfully.');
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $id,
                'action' => 'change order down of categories ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function order_up($id,$order,$level,$parent){
        try{ 
            $id = base64_decode($id);
            $updateval=0;
            $nextorder = $order-1;
           
            $updateval = Category::where('level_id', $level)->where('parent_id', $parent)->where('sort_order', $nextorder)->increment('sort_order');
            $updateval = Category::where('id', $id)->decrement('sort_order');
           
            if ($updateval == 1) {
                return redirect("/administrator/list-categories/".$parent."/".$level)->with('success', 'Order change successfully.');
            } else {
                return redirect("/administrator/list-categories/".$parent."/".$level)->with('error', 'Order not change successfully.');
            }  
        }catch(\Exception $e){ dd($e->getMessage());
            $data = [
                'input_params' => $id,
                'action' => 'change order up of categories ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        } 
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

        $checkExist = Category::where('slug', $urlKey)
                        ->select("id")->count();
        if ($checkExist == 0) {
            return $urlKey;
        }
        $i = 1;
        while ($checkExist > 0) {
            $urlKeyNw = $urlKey . '_' . $i;
            $checkExist = Category::where('slug', $urlKeyNw)
                            ->select("id")->count();

            if ($checkExist == 0) {
                return $urlKeyNw;
            }
            $i++;
        }
    }

}
