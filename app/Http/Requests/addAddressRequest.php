<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class addAddressRequest extends FormRequest
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
            "full_name" => 'required|string',
            "address_title"=> 'required|string',
            "address_line_1"=> 'required|string',
            "city" => 'required|string',
            "state" => 'required|string',
            "country"=>'required|string',
            "pin_code"=>'required|string',
            "contact_no" => 'required|string',
        ];
    }
}
