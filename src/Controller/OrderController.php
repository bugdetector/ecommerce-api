<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/api/order", name="order_list")
     */
    public function list()
    {
        $response = new JsonResponse();
        return $response;
    }

    /**
     * @Route("/api/order", name="order_save", methods={"POST"})
     */
    public function save()
    {
        $response = new JsonResponse();
        return $response;
    }
}