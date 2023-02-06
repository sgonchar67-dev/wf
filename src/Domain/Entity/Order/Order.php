<?php

namespace App\Domain\Entity\Order;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\Employee\GetOrderManagersAction;
use App\Controller\Order\ArchiveOrderAction;
use App\Controller\Order\CancelOrderAction;
use App\Controller\Order\CheckoutOrderAction;
use App\Controller\Order\CompleteOrderAction;
use App\Controller\Order\ConfirmOrderAction;
use App\Controller\Order\CustomerCreateOrderAction;
use App\Controller\Order\EditOrderAction;
use App\Controller\Order\GetOrderStatusesAction;
use App\Controller\Order\NoteOrderAction;
use App\Controller\Order\NotifyOrderAction;
use App\Controller\Order\RefuseOrderAction;
use App\Controller\Order\SeenOrderAction;
use App\Controller\Order\SendOrderAction;
use App\Controller\Order\SupplierCreateOrderAction;
use App\Domain\Entity\Order\OrderEventLog\OrderDataLog;
use App\DTO\Order\OrderActionDto;
use App\Domain\Entity\Cart\Cart;
use App\Domain\Entity\Cart\CartProduct;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Company\Organization;
use App\Domain\Entity\Contractor\Contractor;
use App\Domain\Entity\Delivery\Delivery;
use App\Domain\Entity\Document;
use App\Domain\Entity\Image;
use App\Domain\Entity\Order\OrderEventLog\OrderEvent\Shipment;
use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Domain\Entity\Order\OrderProduct\OrderProduct;
use App\Domain\Entity\Payment\PaymentMethod;
use App\Domain\Entity\User\Profile;
use App\Exception\NotFoundException;
use App\Helper\Doctrine\CollectionHelper;
use App\Helper\ReferenceBook\RbVolumeHelper;
use App\Helper\ReferenceBook\RbWeightHelper;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;


//todo https://api-platform.com/docs/core/security/#hooking-custom-permission-checks-using-voters
#[ApiResource(
    collectionOperations: [
        'get' => [
            'security' => "is_granted('ROLE_ADMIN_PURCHASES') or is_granted('ROLE_ADMIN_SALES')",
        ],
        'get_orders_statuses' => [
            'security' => "is_granted('ROLE_ADMIN_PURCHASES') or is_granted('ROLE_ADMIN_SALES')",
            'method' => 'GET',
            'pagination_enabled' => false,
            'path' => '/orders/statuses',
            'controller' => GetOrderStatusesAction::class,
            'deserialize' => false,
        ],
        'get_orders_managers' => [
            'security' => "is_granted('ROLE_ADMIN_PURCHASES') or is_granted('ROLE_ADMIN_SALES')",
            'method' => 'GET',
            'pagination_enabled' => false,
            'path' => '/orders/managers',
            'controller' => GetOrderManagersAction::class,
            'deserialize' => false,
        ],
        'post_customer_create' => [
            'security' => "is_granted('ROLE_ADMIN_PURCHASES')",
            'method' => 'POST',
            'path' => '/orders/customer_create',
            'requirements' => ['cart'],
            'controller' => CustomerCreateOrderAction::class,
            'denormalization_context' => ['groups' => ['Order:create', 'Order:customer_create']],
        ],
        'post_supplier_create' => [
            'security' => "is_granted('ROLE_ADMIN_SALES')",
            'method' => 'POST',
            'path' => '/orders/supplier_create',
            'requirements' => ['supplierCompany'],
            'controller' => SupplierCreateOrderAction::class,
            'denormalization_context' => ['groups' => ['Order:create', 'Order:supplier_create']],
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => "is_granted('ORDER_VIEW', object)"
        ],
        'delete' => [
            'security' => "is_granted('ORDER_DELETE', object)",
        ],
        'put_note' => [
            'security' => "is_granted('ORDER_NOTE', object)",
            'method' => 'patch',
            'path' => '/orders/{id}/note',
            'controller' => NoteOrderAction::class,
            'denormalization_context' => ['groups' => ['Order:note']],
        ],
        'put_archive' => [
            'security' => "is_granted('ORDER_ARCHIVE', object)",
            'method' => 'PUT',
            'path' => '/orders/{id}/archive',
            'controller' => ArchiveOrderAction::class,
            'denormalization_context' => ['groups' => ['Order:archive']],
        ],
        'put_send' => [
            'security' => "is_granted('ORDER_SEND', object)",
            'method' => 'PUT',
            'path' => '/orders/{id}/send',
            'controller' => SendOrderAction::class,
            'denormalization_context' => ['groups' => ['Order:send']],
        ],
        'patch_edit' => [
            'security' => "is_granted('ORDER_EDIT', object)",
            'method' => 'patch',
            'path' => '/orders/{id}/edit',
            'controller' => EditOrderAction::class,
            'denormalization_context' => ['groups' => ['Order', 'Order:edit']],
        ],
        'put_edit' => [
            'security' => "is_granted('ORDER_EDIT', object)",
            'method' => 'PUT',
            'path' => '/orders/{id}/edit',
            'controller' => EditOrderAction::class,
            'denormalization_context' => ['groups' => ['Order', 'Order:edit']],
        ],
        'put_checkout' => [
            'security' => "is_granted('ORDER_CHECKOUT', object)",
            'method' => 'PUT',
            'path' => '/orders/{id}/checkout',
            'controller' => CheckoutOrderAction::class,
            'denormalization_context' => ['groups' => ['Order:checkout']],
        ],
        'put_seen' => [
            'security' => "is_granted('ORDER_MARK_AS_SEEN', object)",
            'method' => 'PUT',
            'path' => '/orders/{id}/seen',
            'controller' => SeenOrderAction::class,
            'denormalization_context' => ['groups' => ['Order:seen']],
        ],
        'put_refuse' => [
            'security' => "is_granted('ORDER_REFUSE', object)",
            'method' => 'PUT',
            'path' => '/orders/{id}/refuse',
            'controller' => RefuseOrderAction::class,
            'denormalization_context' => ['groups' => ['Order:action']],
            'input' => OrderActionDto::class,
            'denormalize' => false,
        ],
        'put_cancel' => [
            'security' => "is_granted('ORDER_CANCEL', object)",
            'method' => 'PUT',
            'path' => '/orders/{id}/cancel',
            'controller' => CancelOrderAction::class,
            'denormalization_context' => ['groups' => ['Order:action']],
            'input' => OrderActionDto::class,
            'denormalize' => false,
        ],
        'put_confirm' => [
            'security' => "is_granted('ORDER_CONFIRM', object)",
            'method' => 'PUT',
            'path' => '/orders/{id}/confirm',
            'controller' => ConfirmOrderAction::class,
            'denormalization_context' => ['groups' => ['Order:action']],
            'input' => OrderActionDto::class,
            'denormalize' => false,
        ],
        'put_complete' => [
            'security' => "is_granted('ORDER_COMPLETE', object)",
            'method' => 'PUT',
            'path' => '/orders/{id}/complete',
            'controller' => CompleteOrderAction::class,
            'denormalization_context' => ['groups' => ['Order:action']],
            'input' => OrderActionDto::class,
            'denormalize' => false,
        ],
        'put_notify' => [
            'security' => "is_granted('ORDER_NOTIFICATION', object)",
            'method' => 'PUT',
            'path' => '/orders/{id}/notify',
            'controller' => NotifyOrderAction::class,
            'denormalization_context' => ['groups' => ['Order:action']],
            'input' => OrderActionDto::class,
            'denormalize' => false,
        ],
    ],
    attributes: ['pagination_client_items_per_page' => true],
//    denormalizationContext: ['groups' => ['Order', 'Order:create', 'Order:edit']],
    normalizationContext: ['groups' => ['Order', 'Order:read']],
)]
#[ApiFilter(DateFilter::class, properties: ['createdAt' => DateFilter::EXCLUDE_NULL, 'updatedAt'])]
#[ApiFilter(NumericFilter::class, properties: [
    'manager.id', 'status', 'supplierCompany.id', 'customerCompany.id', 'contractor.id', 'shotNumber'
])]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'partial',
    'supplierCompany.name' => 'partial',
    'customerCompany.name' => 'partial',
])]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id',
        'manager.name',
        'manager.user.profile.name',
        'manager.user.profile.surname',
        'manager.user.profile.patronymic',
        'supplierCompany.name',
        'customerCompany.name',
        'status',
        'shotNumber',
        'createdAt',
        'updatedAt',
        'totalPrice',
        'supplierCompany.id',
        'customerCompany.id',
    ],
    arguments: ['orderParameterName' => 'order']
)]
#[ApiFilter(ExistsFilter::class, properties: ['createdAt'])]
#[Entity]
#[Table(name: "`order`")]
class Order implements OrderStatusConstants
{
    public const ACTOR_CUSTOMER = 1; //роль пользователя в сделке - покупатель
    public const ACTOR_SUPPLIER = 2; //роль пользователя в сделке - продавец

    #[Id]
    #[Column]
    #[GeneratedValue]
    #[Groups(['Order:read'])]
    private ?int $id = null;

    #[Column(nullable: true)]
    #[Groups(['Order:read'])]
    private ?int $shotNumber = null;

    #[Column(nullable: true)]
    #[Groups(['Order:read'])]
    private ?string $number = null;

    #[ManyToOne(targetEntity: Company::class)]
    #[JoinColumn(name: 'customer_company_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['Order:read'])]
    private ?Company $customerCompany = null;

    #[ManyToOne(targetEntity: Employee::class)]
    #[JoinColumn(name: 'customer_employee_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[Groups(['Order:read'])]
    #[ApiProperty(readableLink:false)]
    private ?Employee $customerEmployee = null;

    #[ManyToOne(targetEntity: Employee::class)]
    #[JoinColumn(name: 'manager_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[Groups(['Order'])]
    #[ApiProperty(readableLink:false)]
    private ?Employee $manager = null;

    #[ManyToOne(targetEntity: Company::class)]
    #[Groups(['Order:read', 'Order:create'])]
    private ?Company $supplierCompany = null;

    /**
     * @var Contractor|null Покупатель
     */
    #[ManyToOne(targetEntity: Contractor::class, cascade: ['persist'])]
    #[JoinColumn(onDelete: "SET NULL")]
    #[Groups(['Order:read', 'Order:edit'])]
    private ?Contractor $contractor = null;

    #[OneToOne(mappedBy: 'order', targetEntity: Cart::class, cascade: ['persist'])]
    #[Groups(['Order', 'Order:customer_create'])]
    #[ApiProperty(readableLink:false)]
    private ?Cart $cart = null;

    #[Column(name: 'status', type: 'integer')]
    #[Groups(['Order:read'])]
    private ?int $status = null;

    #[Column(nullable: true)]
    #[Groups(['Order:read'])]
    private ?DateTime $createdAt = null;

    #[Column(nullable: true)]
    #[Groups(['Order:read'])]
    private ?DateTime $placedAt = null;

    #[Column(nullable: true)]
    #[Groups(['Order:read'])]
    private ?DateTime $updatedAt = null;

    /**
     * @var Collection<int, OrderProduct>
     */
    #[OneToMany(mappedBy: 'order', targetEntity: OrderProduct::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['Order', 'Order:edit'])]
    #[ApiProperty(readableLink:false)]
    private Collection $orderProducts;

    /** @var Collection<int, OrderEventLog> */
    #[OneToMany(mappedBy: 'order', targetEntity: OrderEventLog::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['Order:read', 'Order:action'])]
    #[ApiSubresource(maxDepth: 1)]
    private Collection $orderEventLogs;

    /** @var Collection<int, OrderDataLog> */
    #[OneToMany(mappedBy: 'order', targetEntity: OrderDataLog::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['Order:read', 'Order:edit'])]
    #[ApiSubresource(maxDepth: 1)]
    private Collection $orderDataLogs;

    /** @var Collection<int, Document> */
    #[ManyToMany(targetEntity: Document::class, cascade: ["persist", "remove"])]
    #[Groups(['Order:read'])]
    private Collection $documents;

    #[Column(name: 'comment', type: 'string', length: 500, nullable: true)]
    #[Groups(['Order', 'Order:edit'])]
    private ?string $comment = null;

    #[Column(type: 'string', length: 500, nullable: true)]
    #[Groups(['Order', 'Order:note'])]
//    #[ApiProperty(security: "is_granted('ACTOR_CUSTOMER', object)")]
    private ?string $customerNote = null;

    #[Column(type: 'string', length: 500, nullable: true)]
    #[Groups(['Order', 'Order:note'])]
//    #[ApiProperty(security: "is_granted('ACTOR_SUPPLIER', object)")]
    private ?string $supplierNote = null;

    #[Column(name: 'currency', type: 'string', length: 3)]
    #[Groups(['Order:read'])]
    private string $currency;

    #[Column(name: 'total_price', type: 'decimal', precision: 20, scale: 2, nullable: true)]
    #[Groups(['Order:read'])]
    private float $totalPrice;

    #[Column(name: 'discount', type: 'decimal', scale: 2, nullable: true)]
    #[Groups(['Order:read'])]
    private ?float $discount;

    #[Column(name: 'vat', type: 'decimal', scale: 2, nullable: true)]
    #[Groups(['Order:read'])]
    private ?float $vat;

    #[Column(name: 'total_volume', type: 'decimal', precision: 20, scale: 2, nullable: true)]
    #[Groups(['Order:read'])]
    private ?float $volume;

    #[Column(name: 'total_weight', type: 'decimal', precision: 20, scale: 2, nullable: true)]
    #[Groups(['Order:read'])]
    private ?float $weight;

    #[Column(name: 'cnt_offers', type: 'integer', nullable: true)]
    #[Groups(['Order:read'])]
    private ?int $count;

    #[Column(nullable: true)]
    #[Groups(['Order:archive'])]
    private ?bool $inSupplierArchive = null;

    #[Column(nullable: true)]
    #[Groups(['Order:archive'])]
    private ?bool $inCustomerArchive = null;

    /** @todo new Entity OrderDelivery */
    /** @deprecated  */
    #[ManyToOne(targetEntity: Delivery::class)]
    #[JoinColumn(name: 'delivery_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['Order'])]
    private ?Delivery $delivery = null;

    /** @deprecated  */
    /** @todo new Entity OrderDelivery */
    #[Column(name: 'delivery_price', type: 'decimal', scale: 2, nullable: true)]
    #[Groups(['Order'])]
    private ?float $deliveryPrice = null;

    #[ManyToOne(targetEntity: PaymentMethod::class)]
    #[JoinColumn(name: 'payment_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['Order'])]
    private ?PaymentMethod $payment = null;

    /** @deprecated use eventLog Instead of */
    #[Column(name: 'last_actor', type: 'integer', nullable: true)]
    private ?int $lastActor = null;

    #[Column(name: 'customer_data', type: 'json', nullable: true)]
    #[Groups(['Order'])]
    #[ApiProperty(readableLink:false)] //, security: "is_granted('CUSTOMER', object)")]
    private ?array $customerData = null;

    #[ManyToOne(targetEntity: Organization::class)]
    #[JoinColumn(name: 'customer_organization_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['Order'])]
    #[ApiProperty(readableLink:false)] //, security: "is_granted('CUSTOMER', object)")]
    private ?Organization $customerOrganization = null;

    #[OneToMany(mappedBy: 'order', targetEntity: Shipment::class, cascade: ['persist', 'remove'])]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Order:read'])]
    private Collection $shipments;

    public function __construct(?Cart $cart = null, ?Company $supplierCompany = null)
    {
        $this->init();
        $this->status = $cart ? self::STATUS_DRAFT_CUSTOMER : self::STATUS_DRAFT_SUPPLIER;
        $this->cart = $cart;
        $this->supplierCompany = $supplierCompany;
    }

    public function __clone()
    {
        if ($this->id !== null) {
            $this->id = null;
            $this->init();
            $this->orderProducts = $this->getOrderProducts()->map(
                fn (OrderProduct $p) => clone $p
            );
        }
    }

    private function init(): self
    {
        $this->status = self::STATUS_DRAFT_CUSTOMER;
        $this->inCustomerArchive = false;
        $this->inSupplierArchive = false;
        $this->currency = 'RUB';
        $this->orderProducts = new ArrayCollection();
        $this->orderEventLogs = new ArrayCollection();
        $this->orderDataLogs = new ArrayCollection();
        $this->documents = new ArrayCollection();
        return $this;
    }

    public static function costumerCreate(Cart $cart, ?Employee $employee = null): self
    {
        return (new self())
            ->checkout($cart, $employee);
    }

    public static function supplierCreate(Company $supplierCompany, ?Employee $employee = null): self
    {
        $self = new self();
        $self->supplierCompany = $supplierCompany;
        $self->manager = $employee;
        return  $self;
    }

    /**
     * @return Contractor|null
     */
    public function getContractor(): ?Contractor
    {
        return $this->contractor;
    }

    /**
     * @param Contractor|null $contractor
     * @return Order
     */
    public function setContractor(?Contractor $contractor): Order
    {
        $this->contractor = $contractor;
        $this->customerCompany = $contractor->getContractorCompany();
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerCompany(): ?Company
    {
        return $this->customerCompany;
    }

    #[Pure] #[Groups(['Order:read'])]
    public function getCustomerCompanyName(): string
    {
        return $this->getCustomerCompany()?->getName();
    }

    #[Pure] #[Groups(['Order:read'])]
    public function getCustomerName(): string
    {
        return $this->getCustomerCompany()?->getName()
            ?: $this->getContractor()?->getName();
    }

    public function getSupplierCompany(): ?Company
    {
        return $this->supplierCompany;
    }

    #[Pure] #[Groups(['Order:read'])]
    public function getSupplierCompanyName(): string
    {
        return $this->getSupplierCompany()?->getName();
    }

    #[Groups(['Order:read'])]
    public function getSupplierCompanyLogo(): ?Image
    {
        return $this->getSupplierCompany()?->getLogo();
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function findOrderProductById($id): ?OrderProduct
    {
        return CollectionHelper::findOneById($this->orderProducts, $id);
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    #[Groups(['Order:read'])]
    public function getVatAmount(): ?float
    {
        $totalPrice = 0;
        foreach ($this->orderProducts as $orderProduct) {
            $totalPrice += $orderProduct->getVatAmount();
        }

        return $totalPrice;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function getVat(): ?float
    {
        return $this->vat;
    }

    public function checkout(Cart $cart, ?Employee $employee = null): Order
    {
        $this->cart = $cart;
        $cart->setOrder($this);
        $this->customerEmployee = $cart->getEmployee();
        $this->manager = $cart->getShowcase()->getCompany()->getUser()->getEmployee();
        $this->customerCompany = $employee ? $employee->getCompany() : $cart->getCustomerCompany();
        $this->supplierCompany = $cart->getShowcase()->getCompany();
        $this->currency = $cart->getShowcase()->getCurrency();
        $this->discount = $cart->getDiscount();
        $this->vat = $cart->getVat();
        $this->updatedAt = new DateTime();

        $this->setOrderProductsFromCart($cart);

        return $this;
    }

    /** @deprecated  */
    #[Groups(['Order:read'])]
    public function getLastActor(): ?int
    {
        return $this->getLastActorCompany() === $this->getSupplierCompany()
            ? Order::ACTOR_SUPPLIER
            : Order::ACTOR_CUSTOMER;
    }

    public function getLastEventLog(): ?OrderEventLog
    {
        return $this->orderEventLogs->last() ?: null;
    }

    public function getLastDataLog(): ?OrderDataLog
    {
        return $this->orderDataLogs->last() ?: null;
    }

    public function getPayment(): ?PaymentMethod
    {
        return $this->payment;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function place(): Order
    {
        $this->status = self::STATUS_PLACED;
        $this->cart?->close();
        if (!$this->createdAt) {
            $this->createdAt = new DateTime();
        }
        $this->placedAt = new DateTime();
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getInSupplierArchive(): ?bool
    {
        return $this->inSupplierArchive;
    }

    /**
     * @return bool|null
     */
    public function getInCustomerArchive(): ?bool
    {
        return $this->inCustomerArchive;
    }

    /**
     * @return DateTimeInterface
     */
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function seen(): self
    {
        $this->status = self::STATUS_SEEN;
        $this->updatedAt = new DateTime();
        return $this;
    }

    public function getLastActorCompany(): ?Company
    {
        return $this->getLastEventLog()?->getCompany();
    }

    public function confirm(): Order
    {
        $this->status = self::STATUS_IN_PROGRESS;
        $this->updatedAt = new DateTime();
        return $this;
    }

    public function complete(): Order
    {
        $this->status = self::STATUS_DONE;
        $this->updatedAt = new DateTime();
        return $this;
    }

    private function setOrderProductsFromCart(Cart $cart): self
    {
        $this->orderProducts->clear();
        $this->orderProducts = $cart->getCartProducts()->map(
            fn(CartProduct $cartProduct) => $this->orderProducts->filter(
                fn(OrderProduct $orderProduct) => $orderProduct->matchToCartProduct($cartProduct)
            )->first() ?: OrderProduct::createByCartProduct($this, $cartProduct)
        );

        return $this->recalculate();
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getCustomerData(): ?array
    {
        return $this->customerData;
    }

    /**
     * @param Collection|OrderProduct[] $orderProducts
     */
    public function setOrderProducts(array|Collection $orderProducts): Order
    {
        $this->orderProducts->clear();
        $this->orderProducts = CollectionHelper::create($orderProducts);
        return $this->recalculate();
    }

    public function recalculate(): self
    {
        return $this
            ->recalculatePrice()
            ->recalculateVolume()
            ->recalculateWeight()
            ->recalculateCount()
        ;
    }

    public function recalculateCount(): self
    {
        $this->count = $this->orderProducts->count();
        return $this;
    }

    public function recalculatePrice(): self
    {
        $this->totalPrice = 0;
        foreach ($this->orderProducts as $orderProduct) {
            $this->totalPrice += $orderProduct->getTotalPrice();
        }

        return $this;
    }

    public function recalculateVolume(): self
    {
        $this->volume = 0;
        foreach ($this->getOrderProducts() as $p) /** @var OrderProduct $p */ {
            $this->volume += RbVolumeHelper::conversionToDefaultFromRbvMeasure(
                    $p->getRbvVolumeMeasure(),
                    $p->getTotalVolume()
                ) + RbVolumeHelper::conversionToDefaultFromRbvMeasure(
                    $p->getProductPackage()?->getRbvVolumeMeasure() ?: $p->getRbvVolumeMeasure(),
                    $p->getProductPackage()?->getVolume() * $p->getProductPackageCount()
                );
        }

        return $this;
    }

    public function recalculateWeight(): self
    {
        $this->weight = 0;
        foreach ($this->getOrderProducts() as $p) /** @var OrderProduct $p */ {
            $this->weight += RbWeightHelper::conversionToDefaultFromRbvMeasure(
                    $p->getRbvWeightMeasure(),
                    $p->getTotalWeight()
                ) + RbWeightHelper::conversionToDefaultFromRbvMeasure(
                    $p->getProductPackage()?->getRbvWeightMeasure() ?: $p->getRbvWeightMeasure(),
                    $p->getProductPackage()?->getWeight() * $p->getProductPackageCount()
                );
        }

        return $this;
    }

    public function setDelivery(Delivery $delivery): Order
    {
        $this->delivery = $delivery;
        return $this;
    }

    public function setPayment(PaymentMethod $payment): Order
    {
        $this->payment = $payment;
        return $this;
    }

    public function setStatus(int $status): Order
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return Collection<int, OrderEventLog>
     */
    public function getOrderEventLogs(): Collection
    {
        return $this->orderEventLogs;
    }

    /**
     * @throws NotFoundException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function findLastOrderEventLogByCompany(Company $company): ?OrderEventLog
    {
        $criteria = Criteria::create()
            ->andWhere(new Comparison('company', Comparison::EQ, $company))
            ->orderBy(["createdAt" => Criteria::ASC]);

        return $this->orderEventLogs->matching($criteria)->first() ?: null;
    }


    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function findLastOrderEventLog(): ?OrderEventLog
    {
        $criteria = Criteria::create()
            ->orderBy(["createdAt" => Criteria::ASC]);

        return $this->orderEventLogs->matching($criteria)->first() ?: null;
    }

    /**
     * @return Collection<int, OrderDataLog>
     */
    public function getOrderDataLogs(): Collection
    {
        return $this->orderDataLogs;
    }

    public function addOrderDataLog(OrderDataLog $orderDataLog): self
    {
        $this->orderDataLogs = CollectionHelper::addItem($this->orderDataLogs, $orderDataLog);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @return Organization|null
     */
    public function getCustomerOrganization(): ?Organization
    {
        return $this->customerOrganization;
    }

    /**
     * @param string|null $comment
     * @return Order
     */
    public function setComment(?string $comment): Order
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @param array|null $customerData
     * @return Order
     */
    public function setCustomerData(?array $customerData): Order
    {
        $this->customerData = $customerData;
        return $this;
    }

    /**
     * @param Organization|null $customerOrganization
     * @return Order
     */
    public function setCustomerOrganization(?Organization $customerOrganization): Order
    {
        $this->customerOrganization = $customerOrganization;
        return $this;
    }

    public function refuse(): static
    {
        $this->status = self::STATUS_REFUSED;
        $this->updatedAt = new DateTime();
        return $this;
    }

    public function cancel(): Order
    {
        $this->status = self::STATUS_IN_PROGRESS;
        $this->updatedAt = new DateTime();
        return $this;
    }

    /**
     * @return Employee|null
     */
    public function getCustomerEmployee(): ?Employee
    {
        return $this->customerEmployee;
    }

    #[Pure] #[Groups(['Order:read'])]
     public function getCustomerEmployeeDetail(): ?Profile
    {
        return $this->getCustomerEmployee()?->getUser()->getProfile();
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    /**
     * @return Employee|null
     */
    public function getManager(): ?Employee
    {
        return $this->manager;
    }

    #[Pure] #[Groups(['Order:read'])]
    public function getManagerDetail(): ?Profile
    {
        return $this->getManager()?->getUser()->getProfile();
    }

    public function archive(Employee $employee): self
    {
        $isCustomerEmployee = $employee->getCompany() === $this->customerCompany;

        if ($isCustomerEmployee) {
            $this->inCustomerArchive = true;
        } else {
            $this->inSupplierArchive = true;
        }
        return $this;
    }

    public function setCustomerEmployee(?Employee $customerEmployee): self
    {
        $this->customerEmployee = $customerEmployee;
        return $this;
    }

    /** @deprecated  */
    public function setLastActor(?int $lastActor): self
    {
        $this->lastActor = $lastActor;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getDeliveryPrice(): ?float
    {
        return $this->deliveryPrice;
    }

    public function setDeliveryPrice(?float $deliveryPrice): self
    {
        $this->deliveryPrice = $deliveryPrice;
        return $this;
    }

    /**
     * @param Collection|Document[] $documents
     */
    public function setDocuments(Collection|array $documents): self
    {
        $this->documents = CollectionHelper::create($documents);
        return $this;
    }

    #[Pure] public function getPartnerCompany(Company $company): ?Company
    {
        return match ($company) {
            $this->getSupplierCompany() => $this->getCustomerCompany(),
            $this->getCustomerCompany() => $this->getSupplierCompany(),
        };
    }

    public function addDocuments(Collection|array $documents): self
    {
        $this->documents = CollectionHelper::addItems($this->documents, $documents);
        return $this;
    }

    public function addDocument(Document $document): self
    {
        $this->documents = CollectionHelper::addItem($this->documents, $document);
        return $this;
    }

    public function addOrderEventLog(OrderEventLog $orderEventLog): self
    {
        $this->orderEventLogs = CollectionHelper::addItem($this->orderEventLogs, $orderEventLog);
        return $this;
    }

    /**
     * @return Collection<int, Shipment>
     */
    public function getShipments(): Collection
    {
        return $this->shipments;
    }

    public function getShotNumber(): ?string
    {
        return $this->shotNumber;
    }

    /**
     * @return string|null
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }


    public function setShotNumber(?int $shotNumber): Order
    {
        $this->shotNumber = $shotNumber;
        return $this;
    }

    public function setNumber(?string $number): Order
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerNote(): ?string
    {
        return $this->customerNote;
    }

    /**
     * @param string|null $customerNote
     * @return Order
     */
    public function setCustomerNote(?string $customerNote): Order
    {
        $this->customerNote = $customerNote;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSupplierNote(): ?string
    {
        return $this->supplierNote;
    }

    /**
     * @param string|null $supplierNote
     * @return Order
     */
    public function setSupplierNote(?string $supplierNote): Order
    {
        $this->supplierNote = $supplierNote;
        return $this;
    }

    /**
     * @param Employee|null $manager
     * @return Order
     */
    public function setManager(?Employee $manager): Order
    {
        $this->manager = $manager;

        return $this;
    }

    public function removeOrderProduct(OrderProduct $orderProduct): static
    {
        if ($this->orderProducts->contains($orderProduct)) {
            $this->orderProducts->removeElement($orderProduct);
            $this->recalculate();
        }

        return $this;
    }

    public function addOrderProduct(OrderProduct $orderProduct): static
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts->add($orderProduct);
            $this->recalculate();
        }

        return $this;
    }

    /**
     * @param \App\Domain\Entity\Company\Company|null $customerCompany
     * @return Order
     */
    public function setCustomerCompany(?Company $customerCompany): Order
    {
        $this->customerCompany = $customerCompany;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getPlacedAt(): ?DateTime
    {
        return $this->placedAt;
    }
}
