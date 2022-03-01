<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $table = 'newsletter_subscriber';
    protected $fillable = [ "name","email_address","is_active","created_at","updated_at"];
      
}
