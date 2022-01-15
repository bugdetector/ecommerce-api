<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DiscountController extends AbstractController
{
    /**
     * @Route("/api/discount", name="discount_list")
     */
    public function list()
    {
        $response = new JsonResponse();
        return $response;
    }

    /**
     * @Route("/api/discount", name="discount_save", methods={"POST"})
     */
    public function save()
    {
        $response = new JsonResponse();
        return $response;
    }
}