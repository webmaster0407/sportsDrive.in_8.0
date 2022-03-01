<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExcludeIps extends Model
{
    protected $table = 'exclude_ips';
    protected $fillable = ["ip","created_at","updated_at"];
}
