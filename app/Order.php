<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table ="orders";
    protected $fillable = ['order_id','invoice_id','customer_id','order_date','total_cart_item','sub_total','total_shipping_amount','discount','total','shipping_address','billing_address','order_status','payment_status','payment_mode','created_at','updated_at','is_completed','is_buy_now'];

    public function addresses(){
        return $this->hasOne('App\Address',"customer_id","customer_id");
    }

    public function customer(){
        return $this->belongsTo('App\Customer',"customer_id","id");
    }

    public function carts(){
        return $this->hasMany('App\Cart',"order_id","id");
    }
}
