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
use App\Customer;

class NewsletterController extends Controller
{
    public function __construct()
    {
       $this->middleware('adminauth');
       // $this->middleware('auth');
    }

    public function listNewsletter(Request $request) {
        try{
        // dd(123);
            $data = Newsletter::orderby("updated_at", "DESC")->get();
            return view('admin.list-newsletter')->with('data', $data);
        }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => $request,
                'action' => 'Admin list Newsletter',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    public function addNewsletter(){
    	try{
	        $data = array(
                "id"=>"",
	            "newsletter_name" => "",
	            "newsletter_subject" => "",
	            "newsletter_desc" =>"",
	            "mode" => "add",
	        );
	        $object1 = (object) $data;
        return view('admin.add-newsletter')->with('data', $object1);
        }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => $request,
                'action' => 'Admin Add Newsletter',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
    public function editNewsletter($id){
    	try{ 
	        $details = Newsletter::where('id',$id)->first();
	        if($details != null){
	        	 $details->mode = 'edit';
	        }
	        return view('admin.add-newsletter')->with('data', $details);
         }catch(\Exception $e){  dd($e->getMessage());
            $data = [
                'input_params' => $request,
                'action' => 'Admin Edit newsletter',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }
     public function addNewsletterData(Request $request){
        $rules = array(
            'newsletter_name' => 'required|string',
            'newsletter_subject' => 'required|string',
            'newsletter_desc' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
       
            $dataInsert = array(
                'newsletter_name' => Input::get('newsletter_name'),
                'newsletter_subject' => Input::get('newsletter_subject'),
                'newsletter_desc' =>Input::get('newsletter_desc'),
                'created_at' =>Carbon::now(),
            );
            $insert = Newsletter::insertGetId($dataInsert);
            if ($insert >0) {
                return redirect('/administrator/list-newsletter')->with('success', 'Newsletter added successfully.');
            } else {
                return redirect('/administrator/list-newsletter')->with('error', 'Newsletter not added successfully.');
            }
        }
    }
     public function updateNewsletterData(Request $request){
        $id  = Input::get('newsletter_id');
        $rules = array(
            'newsletter_name' => 'string',
            'newsletter_subject' => 'string',
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
           return Redirect::back()->withInput()->withErrors($validator);
        } else {
            
             $arrUpdate = array(
                'newsletter_name' => Input::get('newsletter_name'),
                'newsletter_subject' => Input::get('newsletter_subject'),
                'newsletter_desc' =>Input::get('newsletter_desc'),
                'updated_at' =>Carbon::now(),

            );
            $updateval = Newsletter::where('id', $id)->update($arrUpdate);
            if ($updateval == 1) {
                return redirect('/administrator/list-newsletter')->with('success', 'Newsletter updated successfully.');
            } else {
                return redirect('/administrator/list-newsletter')->with('error', 'Newsletter not updated successfully. Please try again');
            }

        }
    }
    public function changeStatusNewsletter(Request $request) {
        try{
            $data = $request->all();

            $operationFlag = $data['operationFlag'];  
            $newsID = $data['chk'];
            $updateVal = 0;
            $message = "Something went wrong!Please try again";
            if ($operationFlag == 'active') {
                $updateVal = Newsletter::whereIn('id', $newsID)->update(array('is_active' => 'Y'));
                $message = "Newsletter/s successfully activated.";
            } else if ($operationFlag == 'deactive') {
                $updateVal = Newsletter::whereIn('id', $newsID)->update(array('is_active' => 'N'));
                $message = "Newsletter/s successfully deactivated.";
            } else if ($operationFlag == 'delete') {
                $updateVal = Newsletter::whereIn('id', $newsID)->delete();
                $message = "Newsletter/s successfully deleted.";
            }else if ($operationFlag == 'customers' || $operationFlag == 'subscribers' || $operationFlag == 'customerAndSubscribers') {
                $updateVal =1;
                $message = "Newsletter sent successfully.";
                $customers = array();
                $subscribers = array();
                $customers = Customer::where('is_active','Y')->where('is_subscriber','Y')->pluck('email_address');
                $subscribers = Subscriber::where('is_active','Y')->pluck('email_address');
                
                if( ($operationFlag == 'customers') && (count($customers) > 0) ){
                    $subsCriberData = $customers;
                }
                elseif($operationFlag == 'subscribers' && count($subscribers) > 0){
                    $subsCriberData = $subscribers;
                }
                elseif($operationFlag == 'customerAndSubscribers'){
                    if( (count($customers) > 0 )&&(count($subscribers) > 0)){
                    $subsCriberData = array_merge($customers,$subscribers);
                    $subsCriberData = array_unique($subsCriberData);
                    }
                    elseif(count($customers) > 0)
                        $subsCriberData = $customers;
                    elseif (count($subscribers) > 0) 
                        $subsCriberData = $subscribers;
                }else{
                    $subsCriberData =null;
                    $updateVal =0;
                    $message = "Subscribers List is Empty. Please try again Later.";
                }

                   foreach ($newsID as  $newsletterID) {
                          $newslettersdata = Newsletter::where('id', $newsletterID)->first();
                        if($newslettersdata->is_active == 'N'){
                            return redirect('/administrator/list-newsletter')->with('error', 'Action not successful. Newsletter is deactivated.');
                         }
                         else{
                             $admin = session('admin');
                             $siteDetails = Admin::select('facebook_url', 'twitter_url', 'googleplus_url', 'instagram_url')->where('id', $admin['id'])->first();
                             $mailSubject = $newslettersdata->newsletter_subject;
                                if($subsCriberData != null){
                                     foreach ($subsCriberData as $subEmail){
                                         Mail::send('admin.emails.newsletter-mail', ['newslettersdata' => $newslettersdata,'siteDetails'=> $siteDetails], function ($message)use($subEmail,$mailSubject) {
                                             $message->from(ENV('NEWSLETTER_EMAIL_ID'),'Sports Drive');
                                             $message->to($subEmail)->subject($mailSubject);
                                         });
                                     }
                                     //make CC 
                                     Mail::send('admin.emails.newsletter-mail', ['newslettersdata' => $newslettersdata,'siteDetails'=> $siteDetails], function ($message)use($subEmail,$mailSubject) {
                                         $message->from(ENV('NEWSLETTER_EMAIL_ID'),'Sports Drive');
                                         $message->cc(ENV('NEWSLETTER_EMAIL_ID'),'Sports Drive')->subject($mailSubject);
                                     });
                                }
                   }
                } 
            }

            if ($updateVal > 0) {
                return redirect("/administrator/list-newsletter")->with('success', $message);
            } else {
                return redirect("/administrator/list-newsletter")->with('error', $message);
            }
        }catch(\Exception $e){ dd($e->getMessage());
            $data = [
                'input_params' => $request->all(),
                'action' => 'change Status of newsletter ',
                'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

    
    
}
