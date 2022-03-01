<?php

namespace App\Http\Requests;

use App\Order;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class GenerateOrder extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //check the order id requested by user is valid and its is of that particular user.
        $order_id= $this->route('order_id');
        $order_id = base64_decode($order_id);
        $user = Auth::user();
        $order = Order::where("customer_id",$user->id)->where("id",$order_id)->first();
        if(!$order)//if order of that user is not found
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
