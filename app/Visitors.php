<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Visitors extends Model
{
    protected $fillable = array("id", "customer_id", "ip_address", "lang", "city", "region", "regionCode", "regionName", "dmaCode", "countryCode", "countryName", "inEU", "euVATrate", "continentCode", "continentName", "latitude", "longitude", "locationAccuracyRadius", "timezone", "currencyCode", "currencySymbol", "currencyConverter", "created_at", "updated_at");
    protected $table = "customer_ips";

    /**
     * Get the user that owns the visitors.
     */
    public function customer()
    {
        return $this->belongsTo('App\Customer',"customer_id","id");
    }
    /**
     * Get the user that owns the visitors.
     */
    public function VisitorsPages()
    {
        return $this->hasMany('App\VisitorsPages',"visitor_id");
    }
    /**
     * Get the user that owns the visitors.
     */
    public function Notifications()
    {
        return $this->hasMany('App\Notifications','visitor_id');
    }
}
