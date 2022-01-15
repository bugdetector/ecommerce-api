<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    /**
     * @Route("/api/customer", name="customer_list")
     */
    public function list()
    {
        $response = new JsonResponse();
        return $response;
    }

    /**
     * @Route("/api/customer", name="customer_save", methods={"POST"})
     */
    public function save()
    {
        $response = new JsonResponse();
        return $response;
    }
}