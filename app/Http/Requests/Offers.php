<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Offers extends FormRequest
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
            "meta_title" => "required|string",
            "meta_keyword" => "required|string",
            "meta_description" => "required|string",
            "short_description" => "required|string",
            "quantity" => "required|int",
            "discount" => "required|int",
            "description" => "required|string",
        ];
    }
}
