<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return array Returns all Category records as array
     */
    public function findAllAsArray() : array{
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return Category|null Returns an Category object matching name
     */
    public function findByName($value) : ?Category
    {
        return $this->findOneBy(["name" => $value]);
    }
}
