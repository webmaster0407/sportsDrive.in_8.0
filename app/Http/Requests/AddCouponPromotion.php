<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCouponPromotion extends FormRequest
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
            "name" => "required|string",
            "discount" => "required|int",
            "coupons_for" => "required|int",
            "valid_till" => "required|string",
            "email_title" => "required|string",
            "banner_image" => "required|image",
        ];
    }
}
