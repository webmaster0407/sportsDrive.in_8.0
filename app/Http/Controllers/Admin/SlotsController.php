<?php

namespace App\Http\Controllers\Admin;

use App\Brand;
use App\coupons;
use App\CouponsProducts;
use App\Product;
use App\Slots;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class SlotsController extends Controller
{
    public function listSlots(){
        try{
            $slots = Slots::get();
            return view('admin.slots.list')->with(compact("slots"));
        } catch (\Exception $ex) {
            $data = [
                'input_params' => null,
                'action' => 'Admin list Coupans',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }

    }

    public function addSlots(Request $request ,$id = null){
        try{
            if($id != null){
                $data = Slots::where("id",$id)->first();
                $data->mode = "edit";
            }else{
                $data = array(
                    "start_date" => "",
                    "end_date" => "",
                    "mode" => "add",
                );
                $data = (object) $data;
            }
            return view('admin.slots.add')->with(compact('data'));
        } catch (\Exception $ex) {
            $data = [
                'input_params' => $request->all(),
                'action' => 'Add slots',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function addSlotsData(Request $request,$id = null){
        try{
            $data = $request->except("_token");
            $validator = Validator::make($data, [
                "start_date" => "required|date",
                "end_date" => "required|date",
            ]);
            if ($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if($id == NULL){
                $insert = Slots::insert($data);
            }else{
                $insert = Slots::where("id",$id)->update($data);
            }
            if($insert){
                return redirect("/administrator/list-slots")->with("success","Slot successfully created/updated");
            }else
                return redirect("/administrator/list-slots")->with("error","There is problem, Please try again");
        } catch (\Exception $ex) {
            $data = [
                'input_params' => $request->all(),
                'action' => 'Admin create slots',
                'exception' => $ex->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);

        }

    }

    public function changeStatusSlot(Request $request){
        try{
            $data = $request->all();
            $operationFlag = $data['operationFlag'];
            $cID = $data['chk'];
            $updateVal = 0;
            $message = "Something went wrong!Please try again";
            if ($operationFlag == 'delete') {
                $updateVal = Slots::whereIn('id', $cID)->delete();
                $message = "Slot/s successfully deleted.";
            }
            if ($updateVal > 0) {
                return redirect("/administrator/list-slots")->with('success', $message);
            } else {
                return redirect("/administrator/list-slots")->with('error', $message);
            }
        }catch(\Exception $e){
            $data = [
                'input_params' => $request->all(),
                'action' => 'change Status of slots ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
}
