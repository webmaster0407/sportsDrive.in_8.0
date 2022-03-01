<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{

    protected $fillable = ['visitorId','user_id','notification','created_at','updated_at'];
    /**
     * Get the user that owns the notifications.
     */
    public function customer()
    {
        return $this->belongsTo('App\Customer',"user_id","id");
    }
}
