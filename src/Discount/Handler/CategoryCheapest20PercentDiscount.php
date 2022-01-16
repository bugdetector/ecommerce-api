<?php

namespace App\Discount\Handler;

use App\Discount\DiscountHandlerAbstract;
use App\Entity\Product;

class CategoryCheapest20PercentDiscount extends DiscountHandlerAbstract{

    public const CATEGORY = 1;
    public const MINIMUM_ITEM_COUNT = 2;
    public const DISCOUNT_PERCENTAGE = 20;

    public function getDiscountReason(): string
    {
        return "BUY_2_GET_20_PERCENT";
    }

    public function getDiscountAmount(): float
    {
        $orderItems = $this->order->getOrderItems();
        $itemCount = 0;
        /** @var Product */
        $chepastItem = null;
        foreach($orderItems as $orderItem){
            if($orderItem->getProduct()->getCategory()->getId() == self::CATEGORY){
                $itemCount += $orderItem->getQuantity();
                if($chepastItem && $chepastItem->getPrice() > $orderItem->getProduct()->getPrice()){
                    $chepastItem = $orderItem->getProduct();
                }
            }
        }
        return $chepastItem ? $chepastItem->getPrice() * self::DISCOUNT_PERCENTAGE / 100 : 0;
    }
}