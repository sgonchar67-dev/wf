<?php

namespace App\Domain\Entity\Order\OrderEventLog\OrderEvent;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Controller\OrderEvent\CreateShipmentAction;
use App\Controller\OrderEvent\AddShipmentItemAction;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\ShipmentItem;
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
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

//#[ApiResource]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
//            'security' => "is_granted('ROLE_USER')",
            'method' => 'post',
            'path' => '/shipments',
            'controller' => CreateShipmentAction::class,
            'denormalization_context' => ['groups' => ['Shipment', 'Shipment:create']],
            'normalization_context' => ['groups' => ['Shipment', 'Shipment:read']],
        ],
        'post_items' => [
//            'security' => "is_granted('ROLE_USER')",
            'method' => 'post',
            'path' => '/shipments/{id}/items',
            'controller' => AddShipmentItemAction::class,
            'denormalization_context' => ['groups' => ['Shipment:addItem']],
            'normalization_context' => ['groups' => ['Shipment', 'Shipment:read']],
        ],
    ],
    normalizationContext: [
        'groups' => ['Shipment', 'Shipment:read'],
    ],
)]
#[Entity]
class Shipment
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    #[Groups(['Shipment:read', 'OrderEventLog:read'])]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Order::class, inversedBy: 'shipments')]
    #[Groups(['Shipment'])]
    private Order $order;

    #[OneToOne(inversedBy: 'shipment', targetEntity: OrderEventLog::class, cascade: ['persist'])]
//    #[Groups(['Shipment:read'])]
    private ?OrderEventLog $orderEventLog = null;

    /** @var Collection<int, ShipmentItem> */
    #[OneToMany(mappedBy: 'shipment', targetEntity: ShipmentItem::class, cascade: ["persist", "remove"])]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Shipment', 'Shipment:create', 'OrderEventLog:read', 'Shipment:read'])]
    private Collection $items;

    /** @var Collection<int, Document> */
    #[ManyToMany(targetEntity: Document::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    #[Groups(['Shipment', 'Shipment:create', 'OrderEventLog:read'])]
    private Collection $documents;

    #[ManyToOne(targetEntity: Delivery::class)]
    #[JoinColumn(name: 'delivery_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['Shipment', 'OrderEventLog:read'])]
    private ?Delivery $delivery = null;

    #[Column(name: 'comment', type: 'string', length: 500, nullable: true)]
    #[Groups(['Shipment', 'OrderEventLog:read'])]
    private ?string $comment = null;

    #[Column(name: 'track_id')]
    #[Groups(['Shipment', 'OrderEventLog:read'])]
    private ?string $trackId = null;

    #[Column(name: 'currency', type: 'string', length: 3)]
    #[Groups(['Shipment:read', 'OrderEventLog:read'])]
    private string $currency = 'RUB';

    #[Column(type: 'decimal', precision: 20, scale: 2, nullable: true)]
    #[Groups(['Shipment:read', 'OrderEventLog:read'])]
    private ?float $amount = null;

    #[Column(name: 'total_volume', type: 'decimal', precision: 20, scale: 2, nullable: true)]
    #[Groups(['Shipment:read', 'OrderEventLog:read'])]
    private ?float $volume = null;

    #[Column(name: 'total_weight', type: 'decimal', precision: 20, scale: 2, nullable: true)]
    #[Groups(['Shipment:read', 'OrderEventLog:read'])]
    private ?float $weight = null;

    #[Pure] public function __construct(Order $order, ?Delivery $delivery = null)
    {
        $this->order = $order;
        $this->delivery = $delivery ?: $order->getDelivery();
        $this->documents = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @return OrderEventLog|null
     */
    public function getOrderEventLog(): mixed
    {
        return $this->orderEventLog;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    #[Groups(['Shipment:read', 'OrderEventLog:read'])]
    public function getDeliveryName(): ?string
    {
        return $this->delivery?->getName();
    }

    /**
     * @param Delivery|null $delivery
     * @return Shipment
     */
    public function setDelivery(?Delivery $delivery): Shipment
    {
        $this->delivery = $delivery;
        return $this;
    }

    /**
     * @return Collection<int, ShipmentItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return float|null
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * @return float|null
     */
    public function getVolume(): ?float
    {
        return $this->volume;
    }

    /**
     * @return float|null
     */
    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setOrderEventLog(OrderEventLog $eventLog): static
    {
        $this->orderEventLog = $eventLog;
        return $this;
    }

    public function addItem(ShipmentItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setShipment($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getDocuments(): Collection
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
     * @return Shipment
     */
    public function setComment(?string $comment): Shipment
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrackId(): ?string
    {
        return $this->trackId;
    }

    /**
     * @param string|null $trackId
     * @return Shipment
     */
    public function setTrackId(?string $trackId): Shipment
    {
        $this->trackId = $trackId;
        return $this;
    }

    /**
     * @param Order $order
     * @return Shipment
     */
    public function setOrder(Order $order): Shipment
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @param Collection|ShipmentItem[] $items
     * @return Shipment
     */
    public function setItems(Collection|array $items): Shipment
    {
        $this->items = CollectionHelper::create($items);
        return $this;
    }
}