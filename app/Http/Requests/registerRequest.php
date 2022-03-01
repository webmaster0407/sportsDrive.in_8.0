<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class registerRequest extends FormRequest
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
		    'password' => 'required|min:6|max:20',
		    're-password' => 'required|min:6|same:password|max:20',
            "first_name" => 'required|string',
            "last_name" => 'required|string',
            "email_address" => 'required|unique:customers,email_address,:email',
            "phone" => 'required|string',
        ];
    }
}
