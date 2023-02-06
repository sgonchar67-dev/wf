<?php

namespace App\Domain\Entity\Cart;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use App\Controller\Cart\CreateCartAction;
use App\Controller\Cart\UpdateCartAction;
use App\Domain\Entity\CollectionTrait;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Order\Order;
use App\Domain\Entity\Showcase\Showcase;
use App\Helper\Doctrine\CollectionHelper;
use App\Helper\ReferenceBook\RbVolumeHelper;
use App\Helper\ReferenceBook\RbWeightHelper;
use App\Service\Cart\dto\CartProductDifferencesDto;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
    collectionOperations: [
        'get' => [
            'security' => "is_granted('ROLE_ADMIN_PURCHASES')"
        ],
        'post' => [
            'security' => "is_granted('ROLE_ADMIN_PURCHASES')",
            'controller' => CreateCartAction::class,
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => "is_granted('CART_ACTION', object)"
        ],
        'put' => [
            'controller' => UpdateCartAction::class,
            'security' => "is_granted('CART_ACTION', object)",
        ],
        'patch' => [
            'security' => "is_granted('CART_ACTION', object)",
        ],
        'delete' => [
            'security' => "is_granted('CART_ACTION', object)",
        ],
    ],
    denormalizationContext: ['groups' => ['Cart', 'Cart:write']],
    normalizationContext: ['groups' => ['Cart', 'Cart:read', 'CartProduct:read', 'rbv:read']],
)]
#[ApiFilter(BooleanFilter::class, properties: ['isClosed'])]
#[Entity]
class Cart
{
    use CollectionTrait;

    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    #[Groups(['Cart:read'])]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Employee::class)]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['Cart:read'])]
    #[ApiFilter(NumericFilter::class, properties: ['employee.id'])]
    private Employee $employee;

    #[ManyToOne(targetEntity: Company::class)]
    #[JoinColumn(name: 'customer_company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['Cart:read'])]
    private Company $customerCompany;

    #[ManyToOne(targetEntity: Showcase::class)]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ApiFilter(NumericFilter::class, properties: ['showcase.id'])]
    #[Groups(['Cart'])]
    private Showcase $showcase;

    #[Column]
    #[Groups(['Cart:read'])]
    private DateTime $createdAt;

    /**
     * @var Collection<int, CartProduct>
     */
    #[OneToMany(mappedBy: 'cart', targetEntity: CartProduct::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Cart'])]
    private Collection $cartProducts;

    #[Column(name: 'currency', type: 'string', length: 3, options: ['default' => 'RUB'])]
    #[Groups(['Cart:read'])]
    private string $currency;

    #[Column(options: ['default' => false])]
    #[Groups(['Cart:read'])]
    private bool $isClosed = false;

    #[Column(name: 'vat', type: 'decimal', scale: 2, nullable: true)]
    #[Groups(['Cart:read'])]
    private ?float $vat = 0.2;

    #[OneToOne(inversedBy: 'cart', targetEntity: Order::class)]
    #[JoinColumn(name: 'order_id', referencedColumnName: 'id', onDelete: "SET NULL")]
    #[Groups(['Cart:read'])]
    private ?Order $order = null;

    public function __construct(Showcase $showcase, ?Employee $employee = null)
    {
        $this->createdAt = new DateTime;
        $this->cartProducts = new ArrayCollection();
        $this->showcase = $showcase;
        $this->currency = $showcase->getCurrency();
        if ($employee) {
            $this->employee = $employee;
        }
    }

    /**
     * @param CartProduct[]|Collection $cartProducts
     */
    public function setCartProducts(Collection|array $cartProducts): self
    {
        $this->cartProducts = $this->createCollection($cartProducts);
        return $this;
    }

    public function addCartProduct(CartProduct $cartProduct): self
    {
        $this->cartProducts = CollectionHelper::addItem($this->cartProducts, $cartProduct);
        return $this;
    }

    public function addCartProducts(Collection|array $cartProducts): self
    {
        $this->cartProducts = CollectionHelper::addItems($this->cartProducts, $cartProducts);
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerCompany(): Company
    {
        return $this->customerCompany;
    }

    public function getShowcase(): Showcase
    {
        return $this->showcase;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, CartProduct>
     */
    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    #[Groups(['Cart:read'])]
    #[Pure] public function getTotalPrice(): ?float
    {
        $totalPrice = 0;
        foreach ($this->cartProducts as $cartProduct) {
            $totalPrice += $cartProduct->getTotalPrice();
        }

        return $totalPrice;
    }

    #[Groups(['Cart:read'])]
    #[Pure] public function getTotalPriceWithDiscount(): ?float
    {
        $totalPrice = 0;
        foreach ($this->cartProducts as $cartProduct) {
            $totalPrice += $cartProduct->getTotalPriceWithDiscount();
        }

        return $totalPrice;
    }

    #[Groups(['Cart:read'])]
    #[Pure] public function getDiscount(): ?float
    {
        $totalPrice = 0;
        foreach ($this->cartProducts as $cartProduct) {
            $totalPrice += $cartProduct->getDiscount();
        }

        return $totalPrice;
    }

    public function getVat(): ?float
    {
        return $this->vat ?: 0.2;
    }

    #[Groups(['Cart:read'])]
    #[Pure] public function getVatAmount(): ?float
    {
        $totalPrice = 0;
        foreach ($this->cartProducts as $cartProduct) {
            $totalPrice += $cartProduct->getVatAmount();
        }

        return $totalPrice;
    }

    public function clear(): self
    {
        $this->cartProducts->clear();
        return $this;
    }

    /**
     * @return CartProductDifferencesDto[]
     */
    #[Groups(['Cart:read'])]
    public function getProductChanges(): array
    {
        $productChanges = [];
        foreach($this->cartProducts as $cartProduct) {
            if ($cartProduct->hasChanges()) {
                $productChanges[] = new CartProductDifferencesDto($cartProduct);
            }
        }

        return $productChanges;
    }

    #[Groups(['Cart:read'])]
    public function getCount(): int
    {
        return $this->getCartProducts()->count();
    }

    #[Groups(['Cart:read'])]
    public function getWeight(): float
    {
        $weight = 0;
        foreach ($this->getCartProducts() as $p) {
            $weight += RbWeightHelper::conversionToDefaultFromRbvMeasure(
                    $p->getRbvWeightMeasure(),
                    $p->getTotalWeight()
                ) + RbWeightHelper::conversionToDefaultFromRbvMeasure(
                    $p->getProductPackage()?->getRbvWeightMeasure() ?: $p->getRbvWeightMeasure(),
                    $p->getProductPackage()?->getWeight() * $p->getProductPackageCount()
                );
        }
        return $weight;
    }

    #[Groups(['Cart:read'])]
    public function getVolume(): float
    {
        $volume = 0;
        foreach ($this->getCartProducts() as $p) {
            $volume += RbVolumeHelper::conversionToDefaultFromRbvMeasure(
                $p->getRbvVolumeMeasure(),
                $p->getTotalVolume()
            ) + RbVolumeHelper::conversionToDefaultFromRbvMeasure(
                $p->getProductPackage()?->getRbvVolumeMeasure() ?: $p->getRbvVolumeMeasure(),
                $p->getProductPackage()?->getVolume() * $p->getProductPackageCount()
            );
        }
        return $volume;
    }

    public function close(): Cart
    {
        $this->isClosed = true;
        return $this;
    }
    public function isClosed(): bool
    {
        return $this->isClosed;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order|null $order
     * @return Cart
     */
    public function setOrder(?Order $order): Cart
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @param Company $customerCompany
     * @return Cart
     */
    public function setCustomerCompany(Company $customerCompany): Cart
    {
        $this->customerCompany = $customerCompany;
        return $this;
    }

    /**
     * @param Showcase $showcase
     * @return Cart
     */
    public function setShowcase(Showcase $showcase): Cart
    {
        $this->showcase = $showcase;
        return $this;
    }

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee $employee
     * @return Cart
     */
    public function setEmployee(Employee $employee): Cart
    {
        $this->employee = $employee;
        $this->customerCompany = $employee->getCompany();
        return $this;
    }
}
