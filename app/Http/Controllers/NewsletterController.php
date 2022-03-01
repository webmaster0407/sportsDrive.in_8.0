<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Http\Requests;
use App\Subscriber;

class NewsletterController extends Controller
{
    public function __construct(){  
        $this->middleware('userauth')->except('subscribeNewsletter');
    }
    public function subscribeNewsletter(Requests\subscribeRequest $request){
        try{
            $data = $request->except('_token','subscribe');
            $isExistSubscriber = Subscriber::where("email_address",$data['email_address'])->first();
            if($isExistSubscriber==null){
                $subscriber = Subscriber::create($data);
                if($subscriber!= null)
                    return redirect("/#newsletter")->with("news-success","You are subscribe for newsletter successfully.");
            }else{
                return redirect("/#newsletter")->with("news-error","You are already  subscribed to our newsletter.");
            }
            return redirect("/#newsletter")->with("news-error","Something went wrong! Please try again.");
        }catch(\Exception $e){
            $data = [
              'input_params' => $request->all(),
              'action' => 'newsletter subscription',
              'exception' => $e->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
    }

	

}
