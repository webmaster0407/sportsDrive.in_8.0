<?php

namespace App\Http\Requests;

use App\Category;
use Illuminate\Foundation\Http\FormRequest;

class ListCategoryProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //check the order id requested by user is valid and its is of that particular user.
        $slug= $this->route('slug');
        $category= Category::where('slug',$slug)->first();
        if(!$category)//if order of that user is not found
            abort(404);
        //otherwise return true
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
            //
        ];
    }
}
