<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
     protected $table = 'addresses';
     protected $fillable = ["address_title","full_name", "address_line_1","address_line_2","city","state","country","contact_no","pin_code","is_default","is_active"];
     public $timestamps = false;
   
}
