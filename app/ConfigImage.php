<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConfigImage extends Model
{
     protected $table = 'configuration_images';
     protected $fillable = ["image",'created_at','updated_at'];
}
