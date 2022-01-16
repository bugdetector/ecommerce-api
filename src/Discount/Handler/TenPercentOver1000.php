<?php

namespace App\Discount\Handler;

use App\Discount\DiscountHandlerAbstract;

class TenPercentOver1000 extends DiscountHandlerAbstract{

    public const MINIMUM_PRICE = 1000;
    public const DISCOUNT_PERCENTAGE = 10;

    public function getDiscountReason(): string
    {
        return "10_PERCENT_OVER_1000";
    }

    public function getDiscountAmount(): float
    {
        return $this->order->getTotal() > self::MINIMUM_PRICE ? 
        $this->order->getTotal() * self::DISCOUNT_PERCENTAGE / 100 : 0;
    }
}