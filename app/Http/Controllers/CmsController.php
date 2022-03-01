<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use DB;
use App\Http\Requests;
use App\Banner;
use App\CmsPage;
use App\Admin;
use App\Customer;
use App\Category;

class CmsController extends Controller
{
    public function __construct(){  
        //$this->middleware('userauth');
        $this->middleware('checkVisitors');
    }
    public function displayCmsPages(Request $request,$slug){
        try{
            $user = Auth::user();

            if($slug =="login" && $user != null){
               return redirect("/");
            }
            $cmsPage = CmsPage::where('slug',$slug)->first();
            $data = Admin::first();
            $footerPages = CmsPage::where("is_active","Y")->where('is_footer','Y')->get();
            $servicePages = CmsPage::where("is_active","Y")->where('is_footer','Y')->where('slug','customer-services')->first();
            if($cmsPage==null)
              abort(404);
            return view('user.cms-page')->with(compact('data','cmsPage','servicePages','footerPages'));
        }catch(\Exception $e){ 
            $data = [
              'input_params' => $slug,
              'action' => 'CMS page ',
              'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

}
