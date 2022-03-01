<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
     protected $table = 'carts';
     protected $fillable = ['order_id', 'product_id', 'configuration_id', 'quantity' , 'price_per_qty', 'total_price', 'discount_per_qty', 'final_price', 'created_at', 'updated_at'];
}
