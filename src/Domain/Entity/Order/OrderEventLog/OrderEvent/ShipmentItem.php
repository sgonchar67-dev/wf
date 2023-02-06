<?php

namespace App\Domain\Entity\Order\OrderEventLog\OrderEvent;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Shipment;
use App\Domain\Entity\Order\OrderProduct\OrderProduct;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['Shipment', 'Shipment:read']],
)]
#[Entity]
class ShipmentItem
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    #[Groups(['Shipment', 'Shipment:create', 'Shipment:read', 'OrderEventLog:read'])]
    private ?int $id = null;

    #[Groups(['Shipment:create'])]
    #[ManyToOne(targetEntity: Shipment::class, inversedBy: 'items')]
    private Shipment $shipment;

    #[Groups(['Shipment', 'Shipment:create', 'Shipment:read', 'OrderEventLog:read'])]
    #[ManyToOne(targetEntity: OrderProduct::class )]
    private OrderProduct $orderProduct;

    #[Column]
    #[Groups(['Shipment', 'Shipment:create', 'Shipment:read', 'OrderEventLog:read'])]
    private ?int $count = null;

    public function __construct(OrderProduct $orderProduct, ?int $count)
    {
        $this->orderProduct = $orderProduct;
        $this->count = $count;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Shipment
     */
    public function getShipment(): Shipment
    {
        return $this->shipment;
    }

    /**
     * @return OrderProduct
     */
    public function getOrderProduct(): OrderProduct
    {
        return $this->orderProduct;
    }

    /**
     * @return int|null
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param Shipment $shipment
     */
    public function setShipment(Shipment $shipment): self
    {
        $this->shipment = $shipment;
        return $this;
    }
}