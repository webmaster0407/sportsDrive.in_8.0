<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
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
use Carbon\Carbon;


class CmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('adminauth');
    }

    public function listPages(Request $request,$pid=null,$lid=null) {
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
                    $level = CmsPage::where("page_id", $parentId)->pluck('level_id');
                    $levelId = intval($level[0]) + 1;
                }
            }

            if($lid != null){
                $levelId = $lid;
            }
            //parent data
            if($parentId != 0){
                $ParentData = CmsPage::where("page_id", $parentId)->first();
                $linkPid = $ParentData->parent_id;
                $linkLid = $ParentData->level_id;
            }
            //super parent data
            if($linkPid !=0){
                $SuperParentData = CmsPage::where("page_id", $linkPid)->first();
            }
            $data = CmsPage::where("parent_id", $parentId)->orderBy('sort_order','ASC')->get();
            foreach ($data as $key => $value) {
               $count[$value['page_id']] = CmsPage::where('parent_id', $value['page_id'])->count();
            }
            $data->parent_id = $parentId;
            $data->level_id = $levelId;
            // $a= Helper::cmsHeaderMenu();
            //$data = CmsPage::orderby("sort_order", "ASC")->get();
            return view('admin.list-pages')->with(compact('data','count','linkLid','linkPid','ParentData','SuperParentData'));
        }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => $request,
                'action' => 'Admin list Cms Pages',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function addPages($pid=null) {
        try{
        $parentId = 0;
            $levelId = 0;
            if($pid != null){
               $parentId = $pid;
               if($parentId == 0){
                 $levelId = 0;
               }
               else{
                    $level = CmsPage::where("page_id", $parentId)->pluck('level_id');
                    $levelId = intval($level[0]) + 1;
                }
            }
        $order = 1;
        $sort_order = CmsPage::whereRaw('sort_order = (select max(`sort_order`) from cms_pages)')->first();
       // dd($sort_order);
        if( $sort_order != null){
            $order =$sort_order['sort_order'] + 1;
        }

        $data = array(
            "parent_id"=>$parentId,
            "level_id" =>$levelId,
            "page_id" => "",
            "page_title" => "",
            "page_subtitle" => "",
            "meta_title" => "",
            "meta_keyword" => "",
			"meta_desc" => "",
            "short_description" => "",
            "description" => "",
            "sort_order" => $order,
            "page_icon" =>"",
            "mode" => "add",
        );
        $object1 = (object) $data;
        return view('admin.add-page')->with('data', $object1);
        }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => Null,
                'action' => 'Admin Add Cms Pages',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function editPage($pageid) {
        try{
            $pagedetails = CmsPage::where('page_id', $pageid)->first();
            if($pagedetails!= null){
                $pagedetails->mode = 'edit';
            }
            return view('admin.add-page')->with('data', $pagedetails);
         }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => Null,
                'action' => 'Admin Edit Cms Pages',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function addPageData(Request $request) {
        try{
        $rules = array(
            'page_title' => 'required|string|min:3|max:50',
            // 'description' => 'required|min:20',
            'meta_title' => 'required|string|min:3|max:50',
            'meta_keyword' => 'required|string|min:3|max:50',
			'meta_desc' => 'required|string|min:3|max:50',
            'page_icon' => 'image|mimes:jpeg,png,jpg|max:2048',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            if(($request->file('page_icon'))){
                $photo = $request->file('page_icon');
                $imageName = uniqid()."".$request->file('page_icon')->getClientOriginalExtension();
                $destinationPath =  public_path(). '/uploads/page_icon/';
                $resizeImagePath = $destinationPath.$ds."56x56";
                if (!file_exists($resizeImagePath)) {
                        File::makeDirectory($resizeImagePath, $mode = 0777, true, true);
                }
                Image::make($photo)->resize(ENV('ICON_W'),ENV('ICON_H'))->save($resizeImagePath.$ds.$imageName);
                Image::make($photo)->save($destinationPath.$ds.$imageName);
                
            }else{
                 $imageName="";
            }

          $slug =   $this->seo_friendly_url(Input::get('page_title'));
          $slug = $this->checkduplicate_URLkey($slug);
            $parentId =Input::get('parent_id');
            $levelId = Input::get('level_id');
            $dataInsert = array(
                'page_title' => Input::get('page_title'),
                'page_subtitle' => Input::get('sub_title'),
                'slug' => $slug,
                'meta_title' => Input::get('meta_title'),
                'meta_keyword' => Input::get('meta_keyword'),
				'meta_desc' => Input::get('meta_desc'),
                'short_description' => Input::get('short_description'),
                'description' => Input::get('description'),
                'sort_order' => Input::get('sort_order'),
                'parent_id'=> $parentId,
                'level_id'=>$levelId,
                'page_icon' =>$imageName,
                'created_at'=>Carbon::now(),
            );

            $insert = CmsPage::insert($dataInsert);
            if ($insert == 1) {
                return redirect('/administrator/list-pages/'.$parentId.'/'.$levelId)->with('success', 'New CMS page added successfully.');
            } else {
                return redirect('/administrator/list-pages/'.$parentId.'/'.$levelId)->with('error', 'New CMS page not added successfully.');
            }
        }
        }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' =>Input::all(),
                'action' => 'add data Cms ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function updatePageData(Request $request) {
        try{
           // dd($request->all());
            $pageID = Input::get('page_id');
            $data  =CmsPage::where('page_id', $pageID)->first();
            $rules = array(
                'page_title' => 'string',
                'meta_title' => 'string',
                'meta_keyword' => 'string',
				'meta_desc' => 'string', 
                // 'description' => 'required|min:20',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                if(($request->file('page_icon'))){ // dd($request->file('page_icon'));
                    $photo = $request->file('page_icon');
                     $ds = DIRECTORY_SEPARATOR;
                    $imageName = uniqid()."".$request->file('page_icon')->getClientOriginalExtension();
                    $destinationPath =  public_path(). '/uploads/page_icon/';
                    $resizeImagePath = $destinationPath.$ds."56x56";
                    if (!file_exists($resizeImagePath)) {
                            File::makeDirectory($resizeImagePath, $mode = 0777, true, true);
                    }
                    Image::make($photo)->resize(ENV('ICON_W'),ENV('ICON_H'))->save($resizeImagePath.$ds.$imageName);
                    Image::make($photo)->save($destinationPath.$ds.$imageName);
                    
                }else{
                     $imageName=Input::get('old_image');
                }
                $dataUpdate = array(
                    'page_title' => Input::get('page_title'),
                    'page_subtitle' => Input::get('sub_title'),
                    'meta_title' => Input::get('meta_title'),
                    'meta_keyword' => Input::get('meta_keyword'),
					'meta_desc' => Input::get('meta_desc'),
                    'short_description' => Input::get('short_description'),
                    'description' => Input::get('description'),
                    'page_icon' =>$imageName,
                );
                $updateval =CmsPage::where('page_id', $pageID)->update($dataUpdate);
                if ($updateval == 1) {
                    return redirect('/administrator/list-pages/'.$data->parent_id.'/'.$data->level_id)->with('success', 'CMS page updated successfully.');
                } else {
                    return redirect('/administrator/list-pages/'.$data->parent_id.'/'.$data->level_id)->with('error', 'Nothing to update.');
                }
            }
        }catch(\Exception $e){dd($e->getMessage());
            $data = [
                'input_params' => $request->all(),
                'action' => 'update data Cms ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function changeStatusCMS(Request $request) {
        try{
            $data = $request->all();
                       
           // $parent_id = CmsPage::where('id' , $data['chk'][0])->pluck('parent_id');
            $operationFlag = $data['operationFlag'];
           
            $cmsID = $data['chk']; 
            $updateVal = 0;
            $featuredCount = 0;
            $message = "Something went wrong!Please try again";

            $featuredCount = CmsPage::where('is_featured','Y')->count();

            if ($operationFlag == 'active') {
                $updateVal = CmsPage::whereIn('page_id', $cmsID)->update(array('is_active' => 'Y'));
                $message = "Cms Page/s successfully activated.";
            } else if ($operationFlag == 'deactive') {
                $updateVal = CmsPage::whereIn('page_id', $cmsID)->update(array('is_active' => 'N'));
                $message = "Cms Page/s successfully deactivated.";
            } else if ($operationFlag == 'delete') {
                $updateVal = CmsPage::whereIn('page_id', $cmsID)->delete();
                $message = "Cms Page/s successfully deleted.";
            } else if ($operationFlag == 'setHeader') {
                $updateVal = CmsPage::whereIn('page_id', $cmsID)->update(array('is_header' => 'Y'));
                $message = "Cms Page/s successfully added to header menu.";
            } else if ($operationFlag == 'unsetHeader') {
                $updateVal = CmsPage::whereIn('page_id', $cmsID)->update(array('is_header' => 'N'));
                $message = "Cms Page/s successfully removed from header menu.";
            }else if ($operationFlag == 'setFeatured' && $featuredCount < 3) {
                $updateVal = CmsPage::whereIn('page_id', $cmsID)->update(array('is_featured' => 'Y'));
                $message = "Cms Page/s successfully added to Featured.";
            } else if ($operationFlag == 'unsetFeatured') {
                $updateVal = CmsPage::whereIn('page_id', $cmsID)->update(array('is_featured' => 'N'));
                $message = "Cms Page/s successfully removed from Featured.";
            }else if ($operationFlag == 'setFooter') {
                $updateVal = CmsPage::whereIn('page_id', $cmsID)->update(array('is_footer' => 'Y'));
                $message = "Cms Page/s successfully added to footer menu.";
            } else if ($operationFlag == 'unsetFooter') {
                $updateVal = CmsPage::whereIn('page_id', $cmsID)->update(array('is_footer' => 'N'));
                $message = "Cms Page/s successfully removed from footer menu.";
            }
            


            if(!isset($data['level_id']))
                $data['level_id'] = 0;
            
          
            if ($updateVal > 0) {
                return redirect("/administrator/list-pages/".$data['parent_id'].'/'.$data['level_id'])->with('success', $message);
            } else {
                return redirect("/administrator/list-pages/".$data['parent_id'].'/'.$data['level_id'])->with('error', $message);
            }
        }catch(\Exception $e){ dd($e->getMessage());
            $data = [
                'input_params' => $request->all(),
                'action' => 'change Status of Cms ',
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
            $updateval =CmsPage::where('sort_order', $nextorder)->decrement('sort_order');
            $updateval = CmsPage::where('page_id',$id)->increment('sort_order');
            
            if ($updateval == 1) {
                return redirect("/administrator/list-pages")->with('success', 'Order change successfully.');
            } else {
                return redirect("/administrator/list-pages")->with('error', 'Order not change successfully.');
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $id,
                'action' => 'change order down of pages ',
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
           
            $updateval = CmsPage::where('sort_order', $nextorder)->increment('sort_order');
            $updateval = CmsPage::where('page_id', $id)->decrement('sort_order');
           
            if ($updateval == 1) {
                return redirect("/administrator/list-pages")->with('success', 'Order change successfully.');
            } else {
                return redirect("/administrator/list-pages")->with('error', 'Order not change successfully.');
            }  
        }catch(\Exception $e){ dd($e->getMessage());
            $data = [
                'input_params' => $id,
                'action' => 'change order up of pages ',
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

        $checkExist = CmsPage::where('slug', $urlKey)
                        ->select("page_id")->count();
        if ($checkExist == 0) {
            return $urlKey;
        }
        $i = 1;
        while ($checkExist > 0) {
            $urlKeyNw = $urlKey . '_' . $i;
            $checkExist = CmsPage::where('slug', $urlKeyNw)
                            ->select("page_id")->count();

            if ($checkExist == 0) {
                return $urlKeyNw;
            }
            $i++;
        }
    }

}
