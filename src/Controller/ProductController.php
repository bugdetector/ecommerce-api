<?php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
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

class ProductController extends AbstractController
{
    /**
     * @Route("/api/product", name="product_list")
     */
    public function list(ProductRepository $productRepository)
    {
        $response = new JsonResponse(
            $productRepository->findAllAsArray()
        );
        return $response;
    }
    /**
     * @Route("/api/product/save", name="product_save", methods={"POST"})
     */
    public function save(Request $request, ValidatorInterface $validator, ManagerRegistry $doctrine)
    {
        $id = $request->get("id");
        $name = $request->get("name");
        $category_id = $request->get("category_id");
        $price = $request->get("price");
        $stock = $request->get("stock");

        $entityManager = $doctrine->getManager();

        /** @var ProductRepository */
        $repository = $entityManager->getRepository(Product::class);
        $categoryRepository = $entityManager->getRepository(Category::class);
        $category = $categoryRepository->find($category_id);
        
        $product = null;
        if($id){
            $product = $repository->find($id);    
        }
        if(!$product){
            $product = $repository->findByName($name) ?: new Product();
        }
        $product->setName($name);
        $product->setCategory($category);
        $product->setPrice($price);
        $product->setStock($stock);
        $errors = $validator->validate($product);
        $response = null;
        if (count($errors) > 0) {    
            $response = new JsonResponse(strval($errors), 406);
        }else{
            $entityManager->persist($product);
            $entityManager->flush();
            $encoders = [new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];

            $serializer = new Serializer($normalizers, $encoders);
            $response = new Response($serializer->serialize($product, "json"), 200, [
                "Content-Type" => "application/json"
            ]);
        }
        return $response;
    }

    /**
     * @Route("/api/product/delete/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Product $product, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($product);
        $entityManager->flush();
        return new JsonResponse([
            "message" => "Product removed."
        ]);
    }
}