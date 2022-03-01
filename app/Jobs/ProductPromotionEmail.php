<?php

namespace App\Jobs;

use App\ProductPromotions;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class ProductPromotionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $promotion_id;
    protected $customer;

    /**
     * test constructor.
     * @param $promotion_id
     * @param $customer
     */
    public function __construct($promotion_id,$customer)
    {
        $this->promotion_id = $promotion_id;
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
        $email = $customer['email_address'];
        $promotion_id = $this->promotion_id;
        $promotion = ProductPromotions::find($promotion_id);
        if($promotion->promotion_type == "C"){
            Mail::send('admin.emails.category_promotion_email', ["customer"=>$customer], function ($m) use ($email) {
                $m->subject('Sports Drive | We have something new for you.');
                $m->from(ENV('MAIL_FROM_EMAIL_ID'),'Sports Drive');
                $m->to($email)->subject('Sports Drive | We have something new for you');
                $m->cc(ENV('MAIL_FROM_EMAIL_ID'),'Sports Drive')->subject('Sports Drive | We have something new for you');
            });
        }else{
            Mail::send('admin.emails.product-promotion-emails', ["customer"=>$customer], function ($m) use ($email) {
                $m->subject('Sports Drive | We have something new for you.');
                $m->from(ENV('MAIL_FROM_EMAIL_ID'),'Sports Drive');
                $m->to($email)->subject('Sports Drive | We have something new for you');
                $m->cc(ENV('MAIL_FROM_EMAIL_ID'),'Sports Drive')->subject('Sports Drive | We have something new for you');
            });
        }
    }
}
