<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/product", name="product_list")
     */
    public function list()
    {
        $response = new JsonResponse();
        return $response;
    }

    /**
     * @Route("/api/product", name="product_save", methods={"POST"})
     */
    public function save()
    {
        $response = new JsonResponse();
        return $response;
    }
}