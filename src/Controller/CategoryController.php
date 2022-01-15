<?php
namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CategoryController extends AbstractController
{
    /**
     * @Route("/api/category", name="category_list")
     */
    public function list(CategoryRepository $categoryRepository)
    {
        $response = new JsonResponse(
            $categoryRepository->findAllAsArray()
        );
        return $response;
    }

    /**
     * @Route("/api/category/save", name="category_save", methods={"POST"})
     */
    public function save(Request $request, ValidatorInterface $validator, ManagerRegistry $doctrine)
    {
        $name = $request->get("name");

        $entityManager = $doctrine->getManager();

        /** @var CategoryRepository */
        $repository = $entityManager->getRepository(Category::class);
        $category = $repository->findByName($name) ?: new Category();
        $category->setName($name);
        $errors = $validator->validate($category);
        $response = null;
        if (count($errors) > 0) {    
            $response = new JsonResponse(strval($errors), 406);
        }else{
            $entityManager->persist($category);
            $entityManager->flush();
            $encoders = [new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];

            $serializer = new Serializer($normalizers, $encoders);
            $response = new Response($serializer->serialize($category, "json"), 200, [
                "Content-Type" => "application/json"
            ]);
        }
        return $response;
    }
}