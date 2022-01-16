<?php
namespace App\Controller;

use App\Discount\DiscountHandlerAbstract;
use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DiscountController extends AbstractController
{
    /**
     * @Route("/api/discount/{id}", name="discount_list", methods={"GET"})
     */
    public function getDiscountForOrder(Order $order)
    {
        $discounts = DiscountHandlerAbstract::checkDiscounts($order);
        $totalDiscount = 0;
        array_walk($discounts, function($el) use (&$totalDiscount){
            $totalDiscount += $el["discountAmount"];
        });
        $response = [
            "orderId" => $order->getId(),
            "discounts" => $discounts,
            "totalDiscount" => $totalDiscount,
            "discountedTotal" => $order->getTotal()
        ];
        return new JsonResponse(
          $response  
        );
    }
}