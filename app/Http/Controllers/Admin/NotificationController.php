<?php

namespace App\Http\Controllers\Admin;

use App\Customer;
use App\Notifications;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function __construct() {
        $this->middleware('adminauth');
    }

    public function listNotifications(Request $request,$id) {
        try {
            $data =    Notifications::where("visitor_id",$id)->orderBy("updated_at","DESC")->with("customer")->paginate(50);
            return view('admin.list-notifications', compact('data'))->with('i', ($request->input('page', 1) - 1) * 10);
        } catch (\Exception $e) {
            $data = [
                'input_params' => $request,
                'action' => 'Admin list Customer Pages',
                'exception' => $e->getMessage(),
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
}
