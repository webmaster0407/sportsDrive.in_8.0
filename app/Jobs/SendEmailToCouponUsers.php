<?php


namespace App\Jobs;


use App\CustomersGroupsUsers;
use App\PromotionsCouponsUsers;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;


class SendEmailToCouponUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $details;
    protected $customer;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details,$customer)
    {
        $this->details = $details;
        $this->customer = $customer;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $customer = $this->customer;
        $data = $this->details;
        $siteDetails =  $customer['siteDetails'];
        $banner_image_name =  $customer['banner_image_name'];
        $discount =  $customer['discount'];
        PromotionsCouponsUsers::insert($data);
        $email = $customer['email_address'];
        Mail::send('admin.emails.send-coupon', ['customer' => $customer,'siteDetails'=>$siteDetails,'data' =>$data,'banner_image_name'=>$banner_image_name], function ($m) use ($email) {
            $m->subject('SportsDrive.In');
            $m->from(ENV('CUSTOMER_SERVICE_EMAIL_ID'),'SportsDrive.In');
            $m->to($email)->subject('SportsDrive.In');
        });
        $m = "$discount% OFF over & above our existing offers, for Registered customers.
Enter your registered mobile number at check out, to activate discount. 
www.SportsDrive.in";
        /*send sms code starts*/
        $apiKey = urlencode('Du0mJi0yXJM-dcQZop4KrMSE0SZP0PTTDfGmCJkxSc');
        $numbers = array($customer['phone']);
        // Message details
        $sender = urlencode('SDrive');
        $numbers = implode(',', $numbers);
        // Prepare data for POST request
        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $m);
        // Send the POST request with cURL
        $ch = curl_init('https://api.textlocal.in/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
        // Process your response here
        /*send sms code ends*/
    }
}