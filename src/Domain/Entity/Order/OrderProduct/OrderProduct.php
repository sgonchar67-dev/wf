<?php

namespace App\Domain\Entity\Order\OrderProduct;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter as APFilter;
use App\Controller\OrderProduct\CreateOrderProductAction;
use App\Controller\OrderProduct\OrderProductAction;
use App\Domain\Entity\Cart\CartProduct;
use App\Domain\Entity\Order\Order;
use App\Domain\Entity\Order\OrderProduct\Embeddable\PackageEmbedded;
use App\Domain\Entity\Product\Product;
use App\Domain\Entity\Product\ProductPackage;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

use App\Domain\Entity\ReferenceBook\ReferenceBookValue;


//todo https://api-platform.com/docs/core/security/#hooking-custom-permission-checks-using-voters
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => "is_granted('ROLE_USER')"],
//        'post' => ['security' => "is_granted('ROLE_USER')"],
        'post' => [
            'security' => "is_granted('ROLE_ADMIN_PURCHASES') or is_granted('ROLE_ADMIN_SALES')",
            'controller' => CreateOrderProductAction::class,
        ],

    ],
    itemOperations: [
        'get' => ['security' => "is_granted('ROLE_USER') and (object.getOrder().getCustomerCompany() == user.getEmployee().getCompany() or object.getOrder().getSupplierCompany() == user.getEmployee().getCompany())"],
        'put' => [
            'security' => "is_granted('ROLE_USER') and (object.getOrder().getCustomerCompany() == user.getEmployee().getCompany() or object.getOrder().getSupplierCompany() == user.getEmployee().getCompany())",
            'controller' => OrderProductAction::class,
        ],
        'patch' => [
            'security' => "is_granted('ROLE_USER') and (object.getOrder().getCustomerCompany() == user.getEmployee().getCompany() or object.getOrder().getSupplierCompany() == user.getEmployee().getCompany())",
            'controller' => OrderProductAction::class,
        ],
        'delete' => [
            'security' => "is_granted('ROLE_USER') and (object.getOrder().getCustomerCompany() == user.getEmployee().getCompany() or object.getOrder().getSupplierCompany() == user.getEmployee().getCompany())",
        ],
    ],
    denormalizationContext: ['groups' => ['OrderProduct', 'OrderProduct:write']],
    normalizationContext: ['groups' => ['OrderProduct', 'OrderProduct:read']],
)]
#[Entity]
//#[HasLifecycleCallbacks]
class OrderProduct
{
    #[Id]
    #[Column(length: 10, options: ['unsigned' => true])]
    #[GeneratedValue]
    #[Groups(['OrderProduct:read'])]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Order::class, inversedBy: 'orderProducts')]
    #[JoinColumn(name: 'order_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ApiFilter(APFilter\NumericFilter::class, properties: ['order.id'])]
    #[Groups(['OrderProduct'])]
    private ?Order $order;

    #[ManyToOne(targetEntity: Product::class)]
    #[JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['OrderProduct'])]
    private ?Product $product;

    #[ManyToOne(targetEntity: ProductPackage::class)]
    #[JoinColumn(name: 'product_package_id', referencedColumnName: 'id', nullable: true)]
    #[ApiProperty(readableLink:false)]
    #[Groups(['OrderProduct'])]
    private ?ProductPackage $productPackage = null;

    #[Embedded(class: PackageEmbedded::class)]
    #[Groups(['OrderProduct:read'])]
    private ?PackageEmbedded $package = null;
    
    #[Column(name: 'code', type: 'string', nullable: true)]
    #[Groups(['OrderProduct:write'])]
    private ?string $code;

    #[Column(name: 'caption', type: 'string', nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private ?string $name;

    #[Column(name: 'article', type: 'string', length: 100, nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private ?string $article;

    #[Column(name: 'quantity', type: 'integer')]
    #[Groups(['OrderProduct'])]
    private int $quantity;

    #[Column(name: 'measure', type: 'string')]
    #[Groups(['OrderProduct:read'])]
    private ?string $measure;

    #[Column(name: 'price', type: 'decimal', precision: 20, scale: 2)]
    #[Groups(['OrderProduct'])]
    #[ApiProperty()]
    private float $price;

    #[Column(name: 'currency', type: 'string', length: 3)]
    #[Groups(['OrderProduct:read'])]
    private string $currency = 'RUB';

    #[Column(name: 'discount', type: 'decimal', precision: 20, scale: 2, nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private ?float $discount = null;

    #[Column(name: 'vat', type: 'decimal', precision: 20, scale: 2, nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private ?float $vat = 0.2;

    #[Column(name: 'weight', type: 'decimal', precision: 20, scale: 4, nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private ?float $weight;

    #[Column(name: 'volume', type: 'decimal', precision: 20, scale: 4, nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private ?float $volume;

    #[ManyToOne(targetEntity: ReferenceBookValue::class)]
    #[JoinColumn(name: 'rbv_weight_measure_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private ?ReferenceBookValue $rbvWeightMeasure;

    #[ManyToOne(targetEntity: ReferenceBookValue::class)]
    #[JoinColumn(name: 'rbv_volume_measure_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private ?ReferenceBookValue $rbvVolumeMeasure;

    public function __construct(Order $order, Product $product, int $quantity, ?ProductPackage $productPackage = null)
    {
        $this->order = $order;
        $this->product = $product;
        $this->quantity = $quantity;
        $this->measure = $product->getRbvMeasure()->getValue();
        $this->name = $product->getName();
        $this->price = $product->getPrice();
        $this->currency = $product->getCurrency() ?: $product?->getShowcase()?->getCurrency() ?: 'RUB';
        $this->productPackage = $productPackage;
        $this->package = new PackageEmbedded($productPackage);

        $this->vat = $product->getVat();
        $this->code = $product->getCode();
        $this->article = $product->getArticle();
        $this->weight = $product->getWeight();
        $this->volume = $product->getVolume();
        $this->rbvVolumeMeasure = $product->getRbvVolumeMeasure();
        $this->rbvWeightMeasure = $product->getRbvWeightMeasure();
    }


    public function __clone()
    {
        $this->id = null;
        $this->order = null;
    }

    public static function createByCartProduct(Order $order, CartProduct $cartProduct): self
    {
        return new self(
            $order,
            $cartProduct->getProduct(),
            $cartProduct->getQuantity(),
            $cartProduct->getProductPackage(),
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getProductPackage(): ?ProductPackage
    {
        return $this->productPackage;
    }

    #[Pure] #[Groups(['OrderProduct:read'])]
    public function getProductPackageDetail(): ?ProductPackage
    {
        return $this->getProductPackage();
    }

    #[Pure] public function getProductPackageQuantity(): int
    {
        return $this->productPackage?->getQuantity() ?: 1;
    }

    #[Pure] public function getProductPackageCount(): int
    {
        return ceil($this->getQuantity() / $this->getProductPackageQuantity());
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    #[Groups(['OrderProduct:read'])]
    public function getTotalPrice(): float
    {
        return $this->price * $this->quantity;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    #[Groups(['OrderProduct:read'])]
    #[Pure] public function getVatAmount(): ?float
    {
        return $this->vat * $this->getTotalPrice();
    }

    public function getVat(): ?float
    {
        return $this->vat;
    }

    #[Groups(['OrderProduct:read'])]
    public function getTotalVolume(): float
    {
        return $this->volume * $this->quantity;
    }

    #[Groups(['OrderProduct:read'])]
    public function getTotalWeight(): float
    {
        return $this->weight * $this->quantity;
    }

    #[Groups(['OrderProduct:read'])]
    #[Pure] public function getPricePerPackage(): float
    {
        return $this->productPackage?->getQuantity() * $this->price;
    }

    #[Pure] public function matchToCartProduct(CartProduct $cartProduct): bool
    {
        return $this->getProduct() === $cartProduct->getProduct()
            && $this->getProductPackage() === $cartProduct->getProductPackage();
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getArticle(): ?string
    {
        return $this->article;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getVolume(): float
    {
        return $this->volume;
    }

    public function getRbvWeightMeasure(): ?ReferenceBookValue
    {
        return $this->rbvWeightMeasure;
    }

    public function getRbvVolumeMeasure(): ?ReferenceBookValue
    {
        return $this->rbvVolumeMeasure;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMeasure(): ?string
    {
        return $this->measure;
    }

    public function edit(int $quantity, ?float $price, ?ProductPackage $productPackage): self
    {
        $this->quantity = $quantity;
        !$price ?: $this->price = $price;
        !$productPackage ?: $this->productPackage = $productPackage;
        return $this;
    }

    /**
     * @return PackageEmbedded|null
     */
    public function getPackage(): ?PackageEmbedded
    {
        return $this->package;
    }


}
