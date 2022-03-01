<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
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
use App\Banner;
use Carbon\Carbon;
use App\Subscriber;
use App\Newsletter;

class SubscriberController extends Controller
{
    public function __construct()
    {
       $this->middleware('adminauth');
       // $this->middleware('auth');
    }

    public function listSubscriber(Request $request) {
        try{ 
            $data = Subscriber::orderby("updated_at", "DESC")->get();
            return view('admin.list-subscriber')->with('data', $data);
        }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => $request,
                'action' => 'Admin list Subscriber Pages',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function addSubscriber(){
    	try{
	        $data = array(
                "id"=>"",
	            "name" => "",
	            "email_address" => "",
	            "mode" => "add",
	        );
	        $object1 = (object) $data;
        return view('admin.add-subscriber')->with('data', $object1);
        }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => $request,
                'action' => 'Admin Add subscriber',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function editSubscriber($id){
    	try{ 
	        $details = Subscriber::where('id',$id)->first();
	        if($details != null){
	        	 $details->mode = 'edit';
	        }
	        return view('admin.add-subscriber')->with('data', $details);
         }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => $request,
                'action' => 'Admin Edit subscriber',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
     public function addSubscriberData(Request $request){
        $rules = array(
            'name' => 'required|string',
            'email_address' => 'required|email',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            $dataInsert = array(
                'name' => Input::get('name'),
                'email_address' => Input::get('email_address'), 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            );
            $insert = Subscriber::insertGetId($dataInsert);
            if ($insert > 0 ) {
                return redirect('/administrator/list-subscriber')->with('success', 'subscriber added successfully.');
            } else {
                return redirect('/administrator/list-subscriber')->with('error', 'subscriber not added successfully.');
            }
        }
    }
     public function updateSubscriberData(Request $request){
        $id  = Input::get('subscriber_id');
        $rules = array(
            'name' => 'string',
            'email_address' => 'email',

        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
           return Redirect::back()->withInput()->withErrors($validator);
        } else {
             $arryUpdate = array(
                'name' => Input::get('name'),
                'email_address' => Input::get('email_address'), 
                'updated_at' => Carbon::now(),
            );
            $updateval = Subscriber::where('id', $id)->update($arryUpdate);
            if ($updateval == 1) {
                return redirect('/administrator/list-subscriber')->with('success', 'Subscriber updated successfully.');
            } else {
                return redirect('/administrator/list-subscriber')->with('error', 'Not updated successfully. Please try again.');
            }
        }
    }
    public function changeStatusSubscriber(Request $request) {
        try{
            $data = $request->all();

            $operationFlag = $data['operationFlag'];
            $sID = $data['chk'];
            $updateVal = 0;
            $message = "Something went wrong!Please try again";
            if ($operationFlag == 'active') {
                $updateVal = Subscriber::whereIn('id', $sID)->update(array('is_active' => 'Y'));
                $message = "Subscriber/s successfully activated.";
            } else if ($operationFlag == 'deactive') {
                $updateVal = Subscriber::whereIn('id', $sID)->update(array('is_active' => 'N'));
                $message = "Subscriber/s successfully deactivated.";
            } else if ($operationFlag == 'delete') {
                $updateVal = Subscriber::whereIn('id', $sID)->delete();
                $message = "Subscriber/s successfully deleted.";
            }

            if ($updateVal > 0) {
                return redirect("/administrator/list-subscriber")->with('success', $message);
            } else {
                return redirect("/administrator/list-subscriber")->with('error', $message);
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'change Status of Subscriber ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

   
}
