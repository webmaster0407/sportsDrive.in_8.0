<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Validator;
use DB;
use App\Http\Requests;
use App\Admin;
use Carbon\Carbon;
use App\Brand;

class BrandController extends Controller
{
    public function __construct()
    {
       $this->middleware('adminauth');
    }

    public function listBrand(Request $request) {
        try{ 
            $data = Brand::orderby("sort_order", "ASC")->get();
            return view('admin.list-brand')->with(compact('data'));
        }catch(\Exception $e){ 
            $data = [
                'input_params' => $request,
                'action' => 'Admin list Brand Pages',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function addBrand(){
    	try{
	        $data = array(
                "id"=>"",
	            "name" => "",
                "slug" => "",
	            "meta_title" => "",
                "meta_keyword"=>"",
                "meta_desc"=>"",
                "short_desc"=>"",
                "image"=>"",
	            "mode" => "add",
	        );
	        $object1 = (object) $data;
        return view('admin.add-brand')->with('data', $object1);
        }catch(\Exception $e){
            $data = [
                'input_params' => $request,
                'action' => 'Admin Add brand',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function editBrand($id){
    	try{ 
	        $details = Brand::where('id',$id)->first();
	        if($details != null){
	        	 $details->mode = 'edit';
	        }
	        return view('admin.add-brand')->with('data', $details);
         }catch(\Exception $e){ 
            $data = [
                'input_params' => $request,
                'action' => 'Admin Edit brand',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
     public function addBrandData(Request $request){
        try{
             $rules = array(
            'name' => 'required|string|unique:brands,name',
            'meta_title'  => 'required|string',
            'meta_keyword'  => 'required|string',          
            'meta_desc' => 'required|string', 
            'image' => 'image|mimes:jpeg,png,jpg|max:2048',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            $data = $request->all();
            $slug =   $this->seo_friendly_url(Input::get('name'));
            $slug = $this->checkduplicate_URLkey($slug);
            $maxSortOrder = Brand::max('sort_order');
            if(($request->file('image'))){
                $photo = $request->file('image');
                $ds = DIRECTORY_SEPARATOR;
                $imageName = uniqid()."".$request->file('image')->getClientOriginalName();
                $destinationPath =  public_path().$ds."uploads".$ds.'brand';

                $resizeImagePath = $destinationPath.$ds."80x80";
                if (!file_exists($resizeImagePath)) {
                        File::makeDirectory($resizeImagePath, $mode = 0777, true, true);
                }
                Image::make($photo)->resize(ENV('BRAND_W'),ENV('BRAND_H'))->save($resizeImagePath.$ds.$imageName);
                Image::make($photo)->save($destinationPath.$ds.$imageName);

             }else{
                 $imageName="";
             }
            $dataInsert = array(
                'name' =>  $data['name'],
                'slug' => $slug,
                'sort_order'=>  $maxSortOrder+1,
                'meta_title' =>  $data['meta_title'],
                'meta_keyword'=>  $data['meta_keyword'],
                'meta_desc' =>  $data['meta_desc'],
                'short_desc' => $data['short_desc'],
                'image'=> $imageName,
                'is_active'=>'Y', 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            );
            $insert = Brand::insertGetId($dataInsert);
            if ($insert > 0 ) {
                return redirect('/administrator/list-brand')->with('success', 'brand added successfully.');
            } else {
                return redirect('/administrator/list-brand')->with('error', 'brand not added successfully.');
            }
        }
        }catch(\Exception $e){ 
            $data = [
                'input_params' => $request,
                'action' => 'addBrandData',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
       
    }
     public function updateBrandData(Request $request){
        try{
                $rules = array(
                    'name' => 'required|string',
                    'meta_title'  => 'required|string',
                    'meta_keyword'  => 'required|string',          
                    'meta_desc' => 'required|string',
                    'image' => 'image|mimes:jpeg,png,jpg|max:2048', 
                );
                $validator = Validator::make(Input::all(), $rules);

                if ($validator->fails()) {
                   return Redirect::back()->withInput()->withErrors($validator);
                } else {
                    $data = $request->all();
                    $id  = $data['id'];
                    $brandData = Brand::where('id', $id)->first();
                    if(($request->file('image'))){
                        $photo = $request->file('image');
                        $ds = DIRECTORY_SEPARATOR;
                        $imageName = uniqid()."".$request->file('image')->getClientOriginalName();
                        $destinationPath =  public_path().$ds."uploads".$ds.'brand';

                        $resizeImagePath = $destinationPath.$ds."80x80";
                        if (!file_exists($resizeImagePath)) {
                                File::makeDirectory($resizeImagePath, $mode = 0777, true, true);
                        }
                        Image::make($photo)->resize(ENV('BRAND_W'),ENV('BRAND_H'))->save($resizeImagePath.$ds.$imageName);
                        Image::make($photo)->save($destinationPath.$ds.$imageName);

                     }else{
                        $imageName=$brandData['image'];
                     }
                    $arryUpdate = array(
                        'name' =>  $data['name'],
                        'meta_title' =>  $data['meta_title'],
                        'meta_keyword'=>  $data['meta_keyword'],
                        'meta_desc' =>  $data['meta_desc'],
                        'short_desc' => $data['short_desc'],
                        'image'=> $imageName,
                        'updated_at' => Carbon::now(),
                    );
                    $updateval = Brand::where('id', $id)->update($arryUpdate);
                    if ($updateval == 1) {
                        return redirect('/administrator/list-brand')->with('success', 'Brand updated successfully.');
                    } else {
                        return redirect('/administrator/list-brand')->with('error', 'Not updated successfully. Please try again.');
                    }
                }
         }catch(\Exception $e){ 
            $data = [
                'input_params' => $request,
                'action' => 'addBrandData',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function changeStatusBrand(Request $request) {
        try{
            $data = $request->all();
            $operationFlag = $data['operationFlag'];
            $bID = $data['chk'];
            $updateVal = 0;
            $message = "Something went wrong!Please try again";
            if ($operationFlag == 'active') {
                $updateVal = Brand::whereIn('id', $bID)->update(array('is_active' => 'Y'));
                $message = "Brand/s successfully activated.";
            } else if ($operationFlag == 'deactive') {
                $updateVal = Brand::whereIn('id', $bID)->update(array('is_active' => 'N'));
                $message = "Brand/s successfully deactivated.";
            } 

            if ($updateVal > 0) {
                return redirect("/administrator/list-brand")->with('success', $message);
            } else {
                return redirect("/administrator/list-brand")->with('error', $message);
            }
        }catch(\Exception $e){ 
            $data = [
                'input_params' => $request->all(),
                'action' => 'change Status of Brand ',
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

        $checkExist = Brand::where('slug', $urlKey)
                        ->select("id")->count();
        if ($checkExist == 0) {
            return $urlKey;
        }
        $i = 1;
        while ($checkExist > 0) {
            $urlKeyNw = $urlKey . '_' . $i;
            $checkExist = Brand::where('slug', $urlKeyNw)
                            ->select("id")->count();

            if ($checkExist == 0) {
                return $urlKeyNw;
            }
            $i++;
        }
    }

    public function order_down($id,$order){
        try{
            $id = base64_decode($id);
            $nextorder = $order+1;
            $updateval=0;
            $updateval =Brand::where('sort_order', $nextorder)->decrement('sort_order');
            $updateval = Brand::where('id',$id)->increment('sort_order');
            
            if ($updateval == 1) {
                return redirect("/administrator/list-brand")->with('success', 'Order change successfully.');
            } else {
                return redirect("/administrator/list-brand")->with('error', 'Order not change successfully.');
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $id,
                'action' => 'change order down of brand ',
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
           
            $updateval = Brand::where('sort_order', $nextorder)->increment('sort_order');
            $updateval = Brand::where('id', $id)->decrement('sort_order');
           
            if ($updateval == 1) {
                return redirect("/administrator/list-brand")->with('success', 'Order change successfully.');
            } else {
                return redirect("/administrator/list-brand")->with('error', 'Order not change successfully.');
            }  
        }catch(\Exception $e){ dd($e->getMessage());
            $data = [
                'input_params' => $id,
                'action' => 'change order up of brand ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        } 
    }


}
