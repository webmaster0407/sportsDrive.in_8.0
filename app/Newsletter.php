<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $table = 'newsletter';
    protected $fillable = [ "newsletter_name","newsletter_subject","newsletter_desc","is_active","created_at","updated_at"];
      
}
