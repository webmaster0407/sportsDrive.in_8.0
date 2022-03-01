<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class register extends FormRequest
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
            'first_name' => 'required|max:10',
            'last_name' => 'required',
			'user_email' => 'required|email',
			'user_password' => 'required|min:6|max:15',
			'phone_no' => 'required|max:20',
			'address' => 'required',
			'state' => 'required',
			'city' => 'required',
        ];
    }
}
