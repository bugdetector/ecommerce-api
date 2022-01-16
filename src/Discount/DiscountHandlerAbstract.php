<?php

namespace App\Discount;

use App\Discount\Handler\Buy5Get1;
use App\Discount\Handler\CategoryCheapest20PercentDiscount;
use App\Discount\Handler\TenPercentOver1000;
use App\Entity\Order;

abstract class DiscountHandlerAbstract{
    protected Order $order;

    private const DISCOUNT_RULES = [
        Buy5Get1::class,
        CategoryCheapest20PercentDiscount::class,
        TenPercentOver1000::class
    ];

    public function __construct(Order $order){
        $this->order = $order;
    }
    abstract function getDiscountReason() : string;
    abstract function getDiscountAmount() : float;

    public function checkDiscount() : ?array{
        if($amount = $this->getDiscountAmount()){
            $this->order->setTotal(
                $this->order->getTotal() - $amount
            );
            return [
                "discountReasoun" => $this->getDiscountReason(),
                "discountAmount" => $this->getDiscountAmount(),
                "subtotal" => $this->order->getTotal()
            ];
        } else {
            return null;
        }
    }

    public static function checkDiscounts(Order $order){
        $discounts = [];
        foreach(self::DISCOUNT_RULES as $rule){
            /** @var DiscountHandlerAbstract */
            $ruleObj = new $rule($order);
            $detail = $ruleObj->checkDiscount();
            if($detail){
                $discounts[] = $detail;
            }
        }
        return $discounts;
    }
}