<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SiteSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [  
            "site_heading" =>'required|string',
            "telephone" =>'required|string',
            "address"  =>'required|string',
            "admin_email" =>'required|email',     
            "facebook_url"=> 'required|url',
            "twitter_url"=> 'required|url',
            "googleplus_url"=> 'required|url',
            "instagram_url" =>'required|url',
            "youtube_url" =>'required|url',
            
        ];
    }
}
