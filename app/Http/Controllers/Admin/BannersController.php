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
use App\Banner;
use Carbon\Carbon;

class BannersController extends Controller
{
    public function __construct()
    {
       $this->middleware('adminauth');
       // $this->middleware('auth');
    }

    public function listBanners(Request $request) {
        try{
            $data = Banner::orderby("sort_order", "ASC")->get();
            return view('admin.list-banners')->with('data', $data);
        }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => $request,
                'action' => 'Admin list Banners Pages',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function addBanners(){
    	try{
	        $data = array(
                "banner_id"=>"",
	            "banner_heading" => "",
	            "banner_images" => "",
	            "banner_url" => "",
	            "banner_text" => "",
                "short_text" => "",
	            "mode" => "add",
	        );
	        $object1 = (object) $data;
        return view('admin.add-banner')->with('data', $object1);
        }catch(\Exception $e){
            $data = [
                'input_params' => NULL,
                'action' => 'Admin Add Banners Pages',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function editBanners($bannersId){
    	try{ //dd($bannersId);
	        //$bannersId = $bannersId;
	        $bannersdetails = Banner::where('banner_id',$bannersId)->first();
	        if($bannersdetails != null){
	        	 $bannersdetails->mode = 'edit';
	        }
	        return view('admin.add-banner')->with('data', $bannersdetails);
         }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => $bannersId,
                'action' => 'Admin Edit Banners Pages',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function addBannersData(Request $request){
        try{
            $rules = array(
            'banners_heading' => 'required|string',
            'banners_url' => 'required|url',
            'bannner_description' =>'required',
            'banners_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
            if(($request->file('banners_image'))){
                $photo = $request->file('banners_image');
                $ds = DIRECTORY_SEPARATOR;
                $imageName = uniqid()."".$request->file('banners_image')->getClientOriginalName();
                $destinationPath =  public_path().$ds."uploads".$ds.'banners';

                $resizeImagePath = $destinationPath.$ds."1280x404";
                if (!file_exists($resizeImagePath)) {
                        File::makeDirectory($resizeImagePath, $mode = 0777, true, true);
                }
                Image::make($photo)->resize(ENV('BANNER_W'),ENV('BANNER_H'))->save($resizeImagePath.$ds.$imageName);
                Image::make($photo)->save($destinationPath.$ds.$imageName);

             }else{
                 $imageName="";
             }
             $maxSortOrder = Banner::max('sort_order');
                $dataInsert = array(
                   // 'banner_id' => $bannersId,
                    'banner_heading' => Input::get('banners_heading'),
                    'banner_images' => $imageName,
                    'banner_url' => Input::get('banners_url'),
                    'short_text' => Input::get('short_text'),
                    'banner_text' => Input::get('bannner_description'),
                    'sort_order' =>  $maxSortOrder+1,
                    'created_at' => DATE('Y-m-d H:i:s'),
                );
                $insert = Banner::insert($dataInsert);
                if ($insert != null) {
                    return redirect('/administrator/list-banners')->with('success', 'Banners added successfully.');
                } else {
                    return redirect('/administrator/list-banners')->with('error', 'Banners not added successfully.');
                }
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'add banners ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function updateBannersData(Request $request){
        try{
            $bannersId  = Input::get('banners_id');
            $rules = array(
                'banners_heading' => 'string',
                'banners_url' => 'url',
                
                'banner_images' => 'image|mimes:jpeg,png,jpg|max:2048',
            );
            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) {
               return Redirect::back()->withInput()->withErrors($validator);
            } else {

                if(($request->file('banners_image'))){
                    $photo = $request->file('banners_image');
                    $ds = DIRECTORY_SEPARATOR;
                    $imageName =uniqid()."".$request->file('banners_image')->getClientOriginalName();
                    $destinationPath =  public_path().$ds."uploads".$ds.'banners';

                   $resizeImagePath = $destinationPath.$ds."1280x404";
                    if (!file_exists($resizeImagePath)) {
                            File::makeDirectory($resizeImagePath, $mode = 0777, true, true);
                    }
                    Image::make($photo)->resize(ENV('BANNER_W'),ENV('BANNER_H'))->save($resizeImagePath.$ds.$imageName);
                    Image::make($photo)->save($destinationPath.$ds.$imageName);

                 }else{
                     $imageName=Input::get('old_image');
                 }

                 $bannersUpdate = array(
                    'banner_heading' => Input::get('banners_heading'),
                    'banner_images' => $imageName,
                    'banner_url' => Input::get('banners_url'),
                    'short_text' => Input::get('short_text'),
                    'banner_text' => Input::get('bannner_description'),
                );
                $updateval = Banner::where('banner_id', $bannersId)
                        ->update($bannersUpdate);
                if ($updateval == 1) {
                    return redirect('/administrator/list-banners')->with('success', 'Banners updated successfully.');
                } else {
                    return redirect('/administrator/list-banners')->with('error', 'Nothing to update.');
                }

            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'update banners ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function changeStatusBanners(Request $request) {
        try{
            $data = $request->all();

            $operationFlag = $data['operationFlag'];
            $bID = $data['chk'];
            $updateVal = 0;
            $message = "Something went wrong!Please try again";
            if ($operationFlag == 'active') {
                $updateVal = Banner::whereIn('banner_id', $bID)->update(array('is_active' => 'Y'));
                $message = "Banner/s successfully activated.";
            } else if ($operationFlag == 'deactive') {
                $updateVal = Banner::whereIn('banner_id', $bID)->update(array('is_active' => 'N'));
                $message = "Banner/s successfully deactivated.";
            } else if ($operationFlag == 'delete') {
                $updateVal = Banner::whereIn('banner_id', $bID)->delete();
                $message = "Banner/s successfully deleted.";
            }

            if ($updateVal > 0) {
                return redirect("/administrator/list-banners")->with('success', $message);
            } else {
                return redirect("/administrator/list-banners")->with('error', $message);
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'change Status of banners ',
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
            $updateval =Banner::where('sort_order', $nextorder)->decrement('sort_order');
            $updateval = Banner::where('banner_id',$id)->increment('sort_order');
            
            if ($updateval == 1) {
                return redirect("/administrator/list-banners")->with('success', 'Order change successfully.');
            } else {
                return redirect("/administrator/list-banners")->with('error', 'Order not change successfully.');
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $id,
                'action' => 'change order down of banners ',
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
           
            $updateval = Banner::where('sort_order', $nextorder)->increment('sort_order');
            $updateval = Banner::where('banner_id', $id)->decrement('sort_order');
           
            if ($updateval == 1) {
                return redirect("/administrator/list-banners")->with('success', 'Order change successfully.');
            } else {
                return redirect("/administrator/list-banners")->with('error', 'Order not change successfully.');
            }  
        }catch(\Exception $e){ dd($e->getMessage());
            $data = [
                'input_params' => $id,
                'action' => 'change order up of banners ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        } 
    }
}
