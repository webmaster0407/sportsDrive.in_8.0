<?php


namespace App\Jobs;


use App\CustomersGroupsUsers;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;


class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $details;
    protected $user;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details,$user)
    {
        $this->details = $details;
        $this->user = $user;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->details['data'];
        $user = $this->user;
        $siteDetails = $this->details['siteDetails'];
        $email = $data['email_address'];
        $password = $data['original_password'];
        $customerGroupsId =  $this->details['customerGroupsId'];
        unset($data['original_password']);
       /* $match = 0;
        $mobile = 0;
        $telArray = array($user[2],$user[6],$user[7],$user[8],$user[9],$user[10]);
        foreach ($telArray as $tel){
            $telSplit = explode(":",$tel);
            $mobile = null;
            if(count($telSplit)>1){
                $mobile = str_replace(" ","",trim($telSplit[1]));
            }else{
                $mobile = str_replace(" ","",$tel);
            }
            $match =  preg_match( '#^\+91[-\s]?[0-9]+$#', $mobile );
            if ($match == 1)
                break;
        }
        if($match == 1){
            $match = explode("+91",$mobile);
            $mobile = $match[1];
        }else
            $mobile = null;
        $data['phone'] = $mobile;*/
        $id = User::insertGetId($data);
        /*insert customer group users #start*/
        CustomersGroupsUsers::insert(array("customer_group_id"=>$customerGroupsId,"user_id"=>$id));
        /*insert customer group users #end*/
        Mail::send('admin.emails.users-registration', ['first_name' => $data['first_name'],'siteDetails'=>$siteDetails,'email_address' =>$email,'password' => $password], function ($m) use ($data,$email) {
            $m->subject('SportsDrive.In | You are registered successfully');
            $m->from(ENV('MAIL_FROM_EMAIL_ID'),'SportsDrive.In ');
            $m->to($email)->subject('SportsDrive.In  | You are registered successfully');
            $m->cc(ENV('MAIL_FROM_EMAIL_ID'),'SportsDrive.In ')->subject('SportsDrive.In  | You are registered successfully');
        });
    }
}