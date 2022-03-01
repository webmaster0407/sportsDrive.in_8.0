<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
     protected $table = 'customers';
     protected $fillable = ["first_name","last_name", "email_address","password","parent_id","level_id","company_name","phone","altphone","defaultaddress","isinactive","is_subscriber","customer_type","date_created","date_updated"];
     public $timestamps = false;

     public function address(){
         return $this->hasOne("App\Address","customer_id","id");
     }
}
