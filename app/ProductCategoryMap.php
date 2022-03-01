<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategoryMap extends Model
{
     protected $table = 'product_category_map';
     protected $fillable = ["product_id","category_id","crated_at","updated_at"];
}
