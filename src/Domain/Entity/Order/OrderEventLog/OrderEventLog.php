<?php

namespace App\Domain\Entity\Order\OrderEventLog;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\EnumOrderEventsType;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Document;
use App\Domain\Entity\Order\Order;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Invoice;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Payment;
use App\Domain\Entity\Order\OrderEventLog\OrderEventConstants;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Refund;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Shipment;
use App\Domain\Entity\User\Profile;
use App\Helper\Doctrine\CollectionHelper;
use DateTime;
use DateTimeInterface;
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
use JetBrains\PhpStorm\Pure;
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
    normalizationContext: ['groups' => ['OrderEventLog', 'OrderEventLog:read']],
)]
#[Entity]
class OrderEventLog implements OrderEventConstants
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    #[Groups(['OrderEventLog:read'])]
    private ?int $id = null;

    #[Column(type: EnumOrderEventsType::NAME)]
    #[Groups(['OrderEventLog:read'])]
    private string $event;

    #[ManyToOne(targetEntity: Order::class, inversedBy: 'orderEventLogs')]
    #[JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['OrderEventLog:read'])]
    private Order $order;

    #[Column(nullable: true)]
    #[Groups(['OrderEventLog:read'])]
    private ?int $orderStatus = null;

    #[Column(name: 'created_at', type: 'datetime')]
    #[Groups(['OrderEventLog:read'])]
    private DateTimeInterface $createdAt;

    #[ManyToOne(targetEntity: Company::class)]
    #[Groups(['OrderEventLog:read'])]
    private Company $company;

    #[ManyToOne(targetEntity: Employee::class)]
    #[JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['OrderEventLog:read'])]
    private ?Employee $employee = null;

    #[Column(name: 'comment', type: 'string', length: 500, nullable: true)]
    #[Groups(['Order:action', 'OrderEventLog:read'])]
    private ?string $comment;

    #[Column(nullable: true)]
    #[Groups(['OrderEventLog:read'])]
    private ?bool $seenByCustomer = null;

    #[Column(nullable: true)]
    #[Groups(['OrderEventLog:read'])]
    private ?bool $seenBySupplier = null;

    /** @var Collection<int, Document>|null */
    #[ManyToMany(targetEntity: Document::class, cascade: ["persist", "remove"])]
    #[Groups(['Order:action', 'OrderEventLog:read'])]
    private ?Collection $documents = null;

    #[OneToOne(mappedBy: 'orderEventLog', targetEntity: Invoice::class)]
    #[Groups(['OrderEventLog:read'])]
    private ?Invoice $invoice = null;

    #[OneToOne(mappedBy: 'orderEventLog', targetEntity: Payment::class)]
    #[Groups(['OrderEventLog:read'])]
    private ?Payment $payment = null;

    #[OneToOne(mappedBy: 'orderEventLog', targetEntity: Refund::class)]
    #[Groups(['OrderEventLog:read'])]
    private ?Refund $refund = null;

    #[OneToOne(mappedBy: 'orderEventLog', targetEntity: Shipment::class)]
    #[Groups(['OrderEventLog:read'])]
    private ?Shipment $shipment = null;

    /**
     * @param Employee $employee
     * @param string $event
     * @param Order $order
     * @param string|null $comment
     * @param Collection|Document[] $documents
     */
    public function __construct(
        Employee         $employee,
        string           $event,
        Order            $order,
        ?string          $comment = null,
        array|Collection $documents = []
    )
    {
        $this->createdAt = new DateTime();
        $this->event = $event;
        $this->order = $order;
        $this->orderStatus = $order->getStatus();
        $this->company = $employee->getCompany();
        $this->employee = $employee;
        $this->comment = $comment;
        $this->documents = CollectionHelper::create($documents);
        $order->addOrderEventLog($this)
            ->addDocuments($this->documents);
    }

    public static function create(
        Employee $employee,
        string   $event,
        Order    $order,
        ?string  $comment = null,
        array    $documents = []
    ): self
    {
        return new self($employee, $event, $order, $comment, $documents);
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    #[Pure] #[Groups(['OrderEventLog:read'])]
    public function getCompanyName(): string
    {
        return $this->company->getName();
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    #[Groups(['OrderEventLog:read'])]
    #[Pure] public function isCustomerActor(): bool
    {
        return $this->company === $this->order->getCustomerCompany();
    }

    #[Groups(['OrderEventLog:read'])]
    #[Pure] public function isSupplierActor(): bool
    {
        return $this->company === $this->order->getSupplierCompany();
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    #[Pure] #[Groups(['OrderEventLog:read'])]
    public function getEmployeeDetail(): Profile
    {
        return $this->getEmployee()?->getUser()->getProfile();
    }


    /**
     * @return bool|null
     */
    public function getSeenByCustomer(): ?bool
    {
        return $this->seenByCustomer;
    }

    /**
     * @param bool|null $seenByCustomer
     * @return OrderEventLog
     */
    public function setSeenByCustomer(?bool $seenByCustomer): OrderEventLog
    {
        $this->seenByCustomer = $seenByCustomer;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getSeenBySupplier(): ?bool
    {
        return $this->seenBySupplier;
    }

    /**
     * @return Invoice|null
     */
    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    /**
     * @return Payment|null
     */
    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    /**
     * @return Shipment|null
     */
    public function getShipment(): ?Shipment
    {
        return $this->shipment;
    }

    /**
     * @return Refund|null
     */
    public function getRefund(): ?Refund
    {
        return $this->refund;
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

    public function getOrderStatus(): ?int
    {
        return $this->orderStatus;
    }

    /**
     * @param Order $order
     * @return OrderEventLog
     */
    public function setOrder(Order $order): OrderEventLog
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @param DateTimeInterface|null $createdAt
     * @return OrderEventLog
     */
    public function setCreatedAt(?DateTimeInterface $createdAt = null): OrderEventLog
    {
        $this->createdAt = $createdAt ?: new DateTime();
        return $this;
    }
}
