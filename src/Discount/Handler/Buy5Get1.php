<?php

namespace App\Discount\Handler;

use App\Discount\DiscountHandlerAbstract;

class Buy5Get1 extends DiscountHandlerAbstract{

    public const CATEGORY = 2;
    public const MINIMUM_ITEM_COUNT = 6;
    public const FREE_ITEM_COUNT = 1;

    public function getDiscountReason(): string
    {
        return "BUY_5_GET_1";
    }

    public function getDiscountAmount(): float
    {
        $orderItems = $this->order->getOrderItems();
        $amount = 0;
        foreach($orderItems as $orderItem){
            if($orderItem->getProduct()->getCategory()->getId() == self::CATEGORY){
               if($orderItem->getQuantity() >= self::MINIMUM_ITEM_COUNT){
                    $amount += $orderItem->getUnitPrice();
               }
            }
        }
        return $amount;
    }
}