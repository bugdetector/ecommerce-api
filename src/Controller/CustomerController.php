<?php
namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController extends AbstractController
{
    /**
     * @Route("/api/customer", name="customer_list")
     */
    public function list(CustomerRepository $customerRepository)
    {
        $response = new JsonResponse(
            $customerRepository->findAllAsArray()
        );
        return $response;
    }

    /**
     * @Route("/api/customer/save", name="customer_save", methods={"POST"})
     */
    public function save(Request $request, ValidatorInterface $validator, ManagerRegistry $doctrine)
    {
        $id = $request->get("id");
        $name = $request->get("name");
        $since = new DateTime($request->get("since"));
        $revenue = $request->get("revenue");

        $entityManager = $doctrine->getManager();

        /** @var CustomerRepository */
        $repository = $entityManager->getRepository(Customer::class);
        
        $customer = null;
        if($id){
            $customer = $repository->find($id);    
        }
        if(!$customer){
            $customer = $repository->findOneBy(["name" => $name]) ?: new Customer();
        }
        $customer->setName($name);
        $customer->setSince($since);
        $customer->setRevenue($revenue);
        $errors = $validator->validate($customer);
        $response = null;
        if (count($errors) > 0) {    
            $response = new JsonResponse(strval($errors), 406);
        }else{
            $entityManager->persist($customer);
            $entityManager->flush();
            $encoders = [new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];

            $serializer = new Serializer($normalizers, $encoders);
            $response = new Response($serializer->serialize($customer, "json"), 200, [
                "Content-Type" => "application/json"
            ]);
        }
        return $response;
    }

    /**
     * @Route("/api/customer/delete/{id}", name="customer_delete", methods={"DELETE"})
     */
    public function delete(Customer $customer, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($customer);
        $entityManager->flush();
        return new JsonResponse([
            "message" => "Customer removed."
        ]);
    }
}