<?php

namespace App\Domain\Entity\Order\OrderEventLog\OrderEvent;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Order\CreateOrderAction;
use App\Controller\OrderEvent\CreateInvoiceAction;
use App\Controller\OrderEvent\CreateShipmentAction;
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
            'path' => '/invoices',
            'controller' => CreateInvoiceAction::class,
        ],
    ],
    denormalizationContext: ['groups' => ['Invoice', 'Invoice:create']],
)]
#[Entity]
class Invoice
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    #[Groups(['Invoice:read', 'OrderEventLog:read'])]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Order::class)]
    #[Groups(['Invoice:create', 'OrderEventLog:read'])]
    private Order $order;

    #[OneToOne(inversedBy: 'invoice', targetEntity: OrderEventLog::class, cascade: ['persist'])]
    #[Groups(['Invoice:read'])]
    private ?OrderEventLog $orderEventLog;

    #[Column(type: 'decimal', scale: 2)]
    #[Groups(['Invoice', 'OrderEventLog:read'])]
    private float $amount;

    #[Column]
    private bool $paid = false;

    /** @var Collection<int, \App\Domain\Entity\Document> */
    #[ManyToMany(targetEntity: Document::class, cascade: ["persist", "remove"])]
    #[Groups(['Invoice', 'OrderEventLog:read'])]
    private Collection $documents;

    #[Column(name: 'comment', type: 'string', length: 500, nullable: true)]
    #[Groups(['Invoice', 'OrderEventLog:read'])]
    private ?string $comment = null;

    public function __construct(Order $order, float $amount, $comment = null)
    {
        $this->documents = new ArrayCollection();
        $this->order = $order;
        $this->amount = $amount;
        $this->comment = $comment;
    }


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderEventLog(): ?OrderEventLog
    {
        return $this->orderEventLog;
    }

    public function setOrderEventLog(mixed $orderEventLog): self
    {
        $this->orderEventLog = $orderEventLog;
        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
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
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->paid;
    }

    /**
     * @param bool $paid
     */
    public function setPaid(bool $paid): void
    {
        $this->paid = $paid;
    }

    /**
     * @return Collection
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    /**
     * @param Collection|\App\Domain\Entity\Document[] $documents
     */
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

    /**
     * @param string|null $comment
     * @return Invoice
     */
    public function setComment(?string $comment): Invoice
    {
        $this->comment = $comment;
        return $this;
    }


}