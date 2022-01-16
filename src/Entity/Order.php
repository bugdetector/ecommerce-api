<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $customer;

    #[ORM\Column(type: 'float')]
    private $total;

    #[ORM\OneToMany(mappedBy: 'orderId', targetEntity: OrderItem::class, orphanRemoval: true, cascade: ["persist"])]
    private $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        $product = $orderItem->getProduct();
        if($product->getStock() < $orderItem->getQuantity()){
            throw new Exception(
                vsprintf("Only %d stock available for product %s.", [
                    $product->getStock(),
                    $product->getName()
                ])
            );
        }else{
            $orderItem->setUnitPrice($product->getPrice());
            $rowTotal = $orderItem->getUnitPrice() * $orderItem->getQuantity();
            $orderItem->setTotal($rowTotal);
            $product->setStock($product->getStock() - $orderItem->getQuantity());
            $this->setTotal(
                $this->getTotal() + $rowTotal
            );
        }
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setOrderId($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        $product = $orderItem->getProduct();
        
        $rowTotal = $orderItem->getUnitPrice() * $orderItem->getQuantity();
        $product->setStock($product->getStock() + $orderItem->getQuantity());
        $this->setTotal(
            $this->getTotal() - $rowTotal
        );

        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrderId() === $this) {
                $orderItem->setOrderId(null);
            }
        }

        return $this;
    }
}
