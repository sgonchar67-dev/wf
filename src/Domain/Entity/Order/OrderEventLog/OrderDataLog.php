<?php

namespace App\Domain\Entity\Order\OrderEventLog;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Entity\Order\Order;
use App\Service\Order\OrderDataLog\dto\OrderDataLogDeliveryDto;
use App\Service\Order\OrderDataLog\dto\OrderDataLogProductDto;
use App\Service\Order\OrderDataLog\dto\OrderDataLogPaymentDto;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [
        'get' => [
            'security' => "is_granted('ROLE_USER')",
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => "is_granted('ROLE_USER')",
        ],
    ],
    normalizationContext: ['groups' => ['OrderDataLog', 'OrderDataLog:read']],
)]
#[Entity]
class OrderDataLog
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    #[Groups(['OrderDataLog:read'])]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Order::class)]
    #[JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['OrderDataLog:read'])]
    private Order $order;

    #[OneToOne(targetEntity: OrderEventLog::class)]
    #[JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['OrderDataLog:read'])]
    private OrderEventLog $orderEventLog;

    #[Column(type: 'json', nullable: true)]
    #[Groups(['OrderDataLog:read'])]
    private ?array $delivery = null;

    #[Column(type: 'json', nullable: true)]
    #[Groups(['OrderDataLog:read'])]
    private ?array $payment = null;

    #[Column(type: 'json', nullable: true)]
    #[Groups(['OrderDataLog:read'])]
    private ?array $products = null;

    public static function createByEventLog(OrderEventLog $orderEventLog): self
    {
        $self = new self();
        $self->order = $orderEventLog->getOrder();
        $self->orderEventLog = $orderEventLog;
        return $self;
    }

    /**
     * @return OrderEventLog
     */
    public function getOrderEventLog(): OrderEventLog
    {
        return $this->orderEventLog;
    }

    /**
     * @param OrderEventLog $orderEventLog
     * @return OrderDataLog
     */
    public function setOrderEventLog(OrderEventLog $orderEventLog): OrderDataLog
    {
        $this->orderEventLog = $orderEventLog;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getDelivery(): ?array
    {
        return $this->delivery;
    }

    /**
     * @param array|OrderDataLogDeliveryDto|null $delivery
     * @return OrderDataLog
     */
    public function setDelivery(array|OrderDataLogDeliveryDto|null $delivery): OrderDataLog
    {
        $this->delivery = (array) $delivery;
        return $this;
    }
    
    public function getPayment(): ?array
    {
        return $this->payment;
    }

    /**
     * @param array|OrderDataLogPaymentDto|null $payment
     * @return OrderDataLog
     */
    public function setPayment(array|OrderDataLogPaymentDto|null $payment): OrderDataLog
    {
        $this->payment = (array) $payment;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getProducts(): ?array
    {
        return $this->products;
    }

    /**
     * @param array|OrderDataLogProductDto[]|null $products
     * @return OrderDataLog
     */
    public function setProducts(?array $products): OrderDataLog
    {
        $this->products = $products;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}