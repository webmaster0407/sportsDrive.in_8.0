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
use App\Banner;
use Carbon\Carbon;
use App\Attribute;
use App\AttributeGroup;

class AtrributesController extends Controller
{
    public function __construct()
    {
       $this->middleware('adminauth');
    }
    public function listAttributes(Request $request) {
        try{
            $data = Attribute::orderby("updated_at", "DESC")->get();
            foreach ($data as $key => $value) {
               $group[$value['id']] = AttributeGroup::select('name')->where('id', $value['group_id'])->first();
            }
            return view('admin.list-attributes')->with(compact('data', 'group'));
        }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => $request,
                'action' => 'Admin list attributes',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }  
       
    public function changeStatusAttributes(Request $request) {
        try{
            $data = $request->all();

            $operationFlag = $data['operationFlag'];
            $bID = $data['chk'];
            $updateVal = 0;
            $message = "Something went wrong!Please try again";
            if ($operationFlag == 'active') {
                $updateVal = Attribute::whereIn('id', $bID)->update(array('is_active' => 'Y'));
                $message = "Attribute/s successfully activated.";
            } else if ($operationFlag == 'deactive') {
                $updateVal = Attribute::whereIn('id', $bID)->update(array('is_active' => 'N'));
                $message = "Attribute/s successfully deactivated.";
            } else if ($operationFlag == 'delete') {
                $updateVal = Attribute::whereIn('id', $bID)->delete();
                $message = "Attribute/s successfully deleted.";
            }
            if ($updateVal > 0) {
                return redirect("/administrator/list-attributes")->with('success', $message);
            } else {
                return redirect("/administrator/list-attributes")->with('error', $message);
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'change Status of Attribute ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    
    public function addAttributes(){
        try{
            $data = array(
                "id"=>"",
                "group_id"=>0,
                "name" => "",
                "hex_color" =>"",
                "mode" => "add",
            );
            $object1 = (object) $data;
            $data = $object1;
            $groupList =AttributeGroup::get();
        return view('admin.add-attribute')->with(compact('data','groupList'));
        }catch(\Exception $e){
            $data = [
                'input_params' => null,
                'action' => 'Admin Add Attribute',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function editAttributes($id){
        try{ 
            $data = Attribute::where('id',$id)->first();
            if($data != null){
                 $data->mode = 'edit';
            }
            $groupList =AttributeGroup::get();

            return view('admin.add-attribute')->with(compact('data','groupList'));
         }catch(\Exception $e){
            $data = [
                'input_params' => $id,
                'action' => 'Admin Edit Attribute',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function addAttributesData(Request $request){
        try{
            $rules = array(
                'name' => 'required|unique:attributes|string',
                'group_id' =>'required',
                'hex_color' => 'string',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $dataInsert = array(
                   'group_id' => Input::get('group_id'),
                    'name' => Input::get('name'),
                    'hex_color' => Input::get('hex_color'),                    
                    'created_at' => DATE('Y-m-d H:i:s'),
                );
                $insert = Attribute::insert($dataInsert);
                if ($insert != null) {
                    return redirect('/administrator/list-attributes')->with('success', 'Attribute added successfully.');
                } else {
                    return redirect('/administrator/list-attributes')->with('error', 'Attribute not added successfully.');
                }
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'Admin Edit Attribute',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function updateAttributesData(Request $request){
        try{ 
            $id  = Input::get('attribute_id'); 
            $rules = array(
                'name' => 'string',
                'hex_color' => 'string',
            );
            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) {
               return Redirect::back()->withInput()->withErrors($validator);
            } else {
                 $arrUpdate = array(
                    // 'group_id' =>Input::get('group_id'),
                    'name' => Input::get('name'),
                    'hex_color' => Input::get('hex_color'),
                );
                $updateval = Attribute::where('id', $id)
                        ->update($arrUpdate);
                if ($updateval == 1) {
                    return redirect('/administrator/list-attributes')->with('success', 'Attribute updated successfully.');
                } else {
                    return redirect('/administrator/list-attributes')->with('error', 'Nothing to update.');
                }

            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'Admin Edit Attribute',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

/*Attributes Group function */ 
    public function listAttributeGroups(Request $request) {
        try{
            $data = AttributeGroup::orderby("created_at", "DESC")->get();
            return view('admin.list-attributes-groups')->with('data', $data);
        }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => $request,
                'action' => 'Admin list attributes Groups',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }  
    public function addAttributesGroups(){
        try{
            $data = array(
                "id"=>"",
                "name" => "",
                "type" => "",
                "mode" => "add",
            );
            $object1 = (object) $data;
        return view('admin.add-attribute-group')->with('data', $object1);
        }catch(\Exception $e){
            $data = [
                'input_params' => null,
                'action' => 'Admin Add AttributeGroup',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function editAttributesGroups($id){
        try{ 
            $details = AttributeGroup::where('id',$id)->first();
            if($details != null){
                 $details->mode = 'edit';
            }
            return view('admin.add-attribute-group')->with('data', $details);
         }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => $id,
                'action' => 'Admin Edit AttributeGroup',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function addAttributesGroupsData(Request $request){
        $rules = array(
            'name' => 'required|string',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else {

             $slug =   $this->seo_friendly_url(Input::get('name'));
             $slug = $this->checkduplicate_URLkey($slug);
           
        
            $dataInsert = array(
              
                'name' => Input::get('name'),  
                'slug' => $slug,   
                'type' => Input::get('type'),
                'created_at' => Carbon::now(),
            );
            $insert = AttributeGroup::insert($dataInsert);
            if ($insert != null) {
                return redirect('/administrator/list-attributes-groups')->with('success', 'Attribute Group added successfully.');
            } else {
                return redirect('/administrator/list-attributes-groups')->with('error', 'Attribute Group not added successfully.');
            }
        }
    }
    public function editAttributesGroupsData(Request $request){
        $id  = Input::get('attributeGroup_id');
        $rules = array(
            'name' => 'string',
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
           return Redirect::back()->withInput()->withErrors($validator);
        } else {
            
             $arrUpdate = array(
                'name' => Input::get('name'),
                'type' => Input::get('type'),
                'updated_at' => Carbon::now(),
            );
            $updateval = AttributeGroup::where('id', $id)
                    ->update($arrUpdate);
            if ($updateval == 1) {
                return redirect('/administrator/list-attributes-groups')->with('success', 'Attribute Group updated successfully.');
            } else {
                return redirect('/administrator/list-attributes-groups')->with('error', 'Nothing to update.');
            }

        }
    }

    public function changeStatusAttributesGroups(Request $request) {
        try{
            $data = $request->all();

            $operationFlag = $data['operationFlag'];
            $bID = $data['chk'];
            $updateVal = 0;
            $message = "Something went wrong!Please try again";
            if ($operationFlag == 'active') {
                $updateVal = AttributeGroup::whereIn('id', $bID)->update(array('is_active' => 'Y'));
                $message = "Attribute Group/s successfully activated.";
            } else if ($operationFlag == 'deactive') {
                $updateVal = AttributeGroup::whereIn('id', $bID)->update(array('is_active' => 'N'));
                $message = "Attribute Group/s successfully deactivated.";
            } else if ($operationFlag == 'delete') {
                $updateVal = AttributeGroup::whereIn('id', $bID)->delete();
                $message = "Attribute Group/s successfully deleted.";
            }

            if ($updateVal > 0) {
                return redirect("/administrator/list-attributes-groups")->with('success', $message);
            } else {
                return redirect("/administrator/list-attributes-groups")->with('error', $message);
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'change Status of attributes-groups ',
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

        $checkExist = AttributeGroup::where('slug', $urlKey)
                        ->select("id")->count();
        if ($checkExist == 0) {
            return $urlKey;
        }
        $i = 1;
        while ($checkExist > 0) {
            $urlKeyNw = $urlKey . '_' . $i;
            $checkExist = AttributeGroup::where('slug', $urlKeyNw)
                            ->select("id")->count();

            if ($checkExist == 0) {
                return $urlKeyNw;
            }
            $i++;
        }
    }

    
}
