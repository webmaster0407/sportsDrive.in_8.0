<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomersGroups extends Model
{
    public function customersGroupsUsers(){
        return $this->hasMany('App\CustomersGroupsUsers',"customer_group_id","id");
    }
}
