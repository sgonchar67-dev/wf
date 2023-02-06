<?php

namespace App\Domain\Entity\Order\OrderEventLog\OrderEvent;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\OrderEvent\CreateInvoiceAction;
use App\Controller\OrderEvent\CreatePaymentAction;
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
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'security' => "is_granted('ROLE_USER')",
            'method' => 'post',
            'path' => '/payments',
            'controller' => CreatePaymentAction::class,
            'denormalization_context' => ['groups' => ['Payment', 'Payment:create']],
        ],
    ],
)]
#[Entity]
class Payment
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    #[Groups(['Payment:read', 'OrderEventLog:read'])]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Order::class)]
    #[Groups(['Payment', 'OrderEventLog:read'])]
    private Order $order;

    #[Column(type: 'decimal', scale: 2)]
    #[Groups(['Payment', 'OrderEventLog:read'])]
    private float $amount;

    #[OneToOne(inversedBy: 'payment', targetEntity: OrderEventLog::class, cascade: ['persist'])]
    private ?OrderEventLog $orderEventLog = null;

    /** @var Collection<int, Document> */
    #[ManyToMany(targetEntity: Document::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    #[Groups(['Payment', 'OrderEventLog:read'])]
    private Collection $documents;

    #[Column(name: 'comment', type: 'string', length: 500, nullable: true)]
    #[Groups(['Payment', 'OrderEventLog:read'])]
    private ?string $comment = null;

    #[Pure] public function __construct(Order $order, float $amount, ?string $comment = null)
    {
        $this->amount = $amount;
        $this->order = $order;
        $this->comment = $comment;
        $this->documents = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \App\Domain\Entity\Order\OrderEventLog\OrderEventLog|false|mixed|null
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
     * @return \App\Domain\Entity\Order\Order
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
    public function getDocuments(): ArrayCollection|Collection
    {
        return $this->documents;
    }

    /**
     * @param Collection|Document[] $documents
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
     * @return Payment
     */
    public function setComment(?string $comment): Payment
    {
        $this->comment = $comment;
        return $this;
    }


}