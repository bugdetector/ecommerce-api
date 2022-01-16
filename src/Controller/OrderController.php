<?php
namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderController extends AbstractController
{
    /**
     * @Route("/api/order", name="order_list")
     */
    public function list(OrderRepository $orderRepository)
    {
        $response = new JsonResponse(
            $orderRepository->findAllAsArray()
        );
        return $response;
    }

    /**
     * @Route("/api/order/save", name="order_save", methods={"POST"})
     */
    public function save(Request $request, ValidatorInterface $validator, ManagerRegistry $doctrine)
    {
        $customerId = $request->get("customer");
        $items = $request->get("items");

        $entityManager = $doctrine->getManager();
        /** @var ProductRepository */
        $productRepository = $entityManager->getRepository(Product::class);
        /** @var CustomerRepository */
        $customerRepository = $entityManager->getRepository(Customer::class);
        $customer = $customerRepository->find($customerId);
        if(!$customer){
            return new JsonResponse([
                "status" => "fail",
                "message" => "Customer not exist."
            ], 406);
        }

        $order = new Order();
        $order->setCustomer($customer);
        
        $errors = $validator->validate($order);
        $response = null;
        if (count($errors) > 0) {    
            $response = new JsonResponse(strval($errors), 406);
        }else{
            try{
                foreach($items as $item){
                    $orderItem = new OrderItem();
                    $product = $productRepository->find($item["product"]);
                    $orderItem->setProduct($product);
                    $orderItem->setQuantity($item["quantity"]);
                    $order->addOrderItem($orderItem);
                    $errors = $validator->validate($order);
                    if (count($errors) > 0) {    
                        return new JsonResponse(strval($errors), 406);
                    }
                }
                $entityManager->persist($order);
                $entityManager->flush();
                $response = new JsonResponse([
                    "status" => "ok",
                    "message" => vsprintf("Order %d saved successfully.", [$order->getId()])
                ]);
            }catch(Exception $ex){
                return new JsonResponse([
                    "status" => "fail",
                    "message" => $ex->getMessage()
                ], 406);
            }
        }
        return $response;
    }

    /**
     * @Route("/api/order/delete/{id}", name="order_delete", methods={"DELETE"})
     */
    public function delete(Order $order, EntityManagerInterface $entityManager)
    {
        foreach($order->getOrderItems() as $orderItem){
            $order->removeOrderItem($orderItem);
        }
        $entityManager->remove($order);
        $entityManager->flush();
        return new JsonResponse([
            "message" => "Order removed."
        ]);
    }
}