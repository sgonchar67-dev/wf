<?php

namespace App\Domain\Entity\Order\OrderEventLog\OrderEvent;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\OrderEvent\CreateInvoiceAction;
use App\Controller\OrderEvent\CreateRefundAction;
use App\Domain\Entity\Delivery\Delivery;
use App\Domain\Entity\Document;
use App\Domain\Entity\Order\Order;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Domain\Entity\Order\OrderEventLog\OrderEventConstants;
use App\Helper\Doctrine\CollectionHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'security' => "is_granted('ROLE_USER')",
            'method' => 'post',
            'path' => '/refunds',
            'controller' => CreateRefundAction::class,
        ],
    ],
    denormalizationContext: ['groups' => ['Refund', 'Refund:write']],
)]
#[Entity]
class Refund
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    #[Groups(['Refund', 'OrderEventLog:read'])]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Order::class)]
    #[Groups(['Refund', 'OrderEventLog:read'])]
    private Order $order;

    #[OneToOne(inversedBy: 'refund', targetEntity: OrderEventLog::class)]
    private ?OrderEventLog $orderEventLog;

    #[Column(type: 'decimal', scale: 2)]
    #[Groups(['Refund', 'OrderEventLog:read'])]
    private float $amount;

    /** @var Collection<int, Document> */
    #[ManyToMany(targetEntity: Document::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    #[Groups(['Refund', 'OrderEventLog:read'])]
    private Collection $documents;

    #[Column(name: 'comment', type: 'string', length: 500, nullable: true)]
    #[Groups(['Refund', 'OrderEventLog:read'])]
    private ?string $comment = null;

    #[ManyToOne(targetEntity: Delivery::class)]
    #[JoinColumn(name: 'delivery_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['Refund', 'OrderEventLog:read'])]
    private ?Delivery $delivery = null;

    public function __construct(Order $order, float $amount)
    {
        $this->documents = new ArrayCollection();
        $this->order = $order;
        $this->amount = $amount;
        $this->orderEventLog = $this->order->getOrderEventLogs()
            ->filter(fn(OrderEventLog $e) => $e->getEvent() == OrderEventConstants::EVENT_BILLING)
            ->first() ?: null;
    }


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return OrderEventLog|false|mixed|null
     */
    public function getOrderEventLog(): mixed
    {
        return $this->orderEventLog;
    }

    /**
     * @param \App\Domain\Entity\Order\OrderEventLog\OrderEventLog|false|mixed|null $orderEventLog
     */
    public function setOrderEventLog(mixed $orderEventLog): void
    {
        $this->orderEventLog = $orderEventLog;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param \App\Domain\Entity\Order\Order $order
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return Collection
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function setDocuments(Collection|array $documents): self
    {
        $this->documents = CollectionHelper::create($documents);
        $this->order->addDocuments($documents);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return Delivery|null
     */
    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    /**
     * @param Delivery|null $delivery
     * @return Refund
     */
    public function setDelivery(?Delivery $delivery): Refund
    {
        $this->delivery = $delivery;
        return $this;
    }

}