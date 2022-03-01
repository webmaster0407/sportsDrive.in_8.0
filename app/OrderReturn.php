<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    protected $table ="order_return";
	protected $fillable = ['id','order_id','reason','comments','is_return','is_cancel','created_at','updated_at'];
}
