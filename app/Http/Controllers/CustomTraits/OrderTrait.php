<?php
namespace App\Http\Controllers\CustomTraits;


use App\Attribute;
use App\StatusMaster;

trait OrderTrait{



   public function getOrderNote($status){
       $currentStatus = StatusMaster::where("status_id", $status)->first();
        if($currentStatus->slug == "pending")
            $message = "Your Order have been placed successfully and we are processing your order.";
        elseif($currentStatus->slug == "order_packed")
            $message = "Your order has been packed and Ready to Ship.";
        elseif($currentStatus->slug == "order_shipped")
            $message = "Your order has been shipped.";
        elseif($currentStatus->slug == "order_delivered" || $currentStatus->slug == "delivered")
            $message = "Your order has been Delivered.";
        elseif($currentStatus->slug == "cancelled")
            $message = "Your order has been Cancelled.";
        else
            $message = "Something went wrong while processing your order! Please contact to our customer care";
        return $message;
   }

   public function getOrderId($orderId){
        $orderIdLength = strlen((string)$orderId);
        $requiredOrderIdLength = ENV('ORDERIDLENGHT1');
       $totalZeroesRequired = $requiredOrderIdLength-$orderIdLength;
       $string = null;
       for ($i = 0;$i<$totalZeroesRequired;$i++)
           $string= $string."0";
       $userShownOrderId = "OD".$string.$orderId;
       return $userShownOrderId;
   }
}
