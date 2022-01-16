<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findAllAsArray(){
        return $this->createQueryBuilder("o")
        ->innerJoin('o.customer', 'customer')
        ->addSelect("partial customer.{id,name}")
        ->innerJoin('o.orderItems', 'order_item')
        ->innerJoin('order_item.product', 'product')
        ->addSelect('partial order_item.{id,quantity,unitPrice,total}')
        ->addSelect("partial product.{id,name}")
        ->getQuery()
        ->getArrayResult();
    }
}
