<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerCoupons extends Model
{
    public function coupons(){
        return $this->belongsTo('App\coupons',"coupon_id");
    }
}
