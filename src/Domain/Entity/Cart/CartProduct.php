<?php

namespace App\Domain\Entity\Cart;

use App\Controller\Cart\CreateCartProductAction;
use App\Domain\Entity\Cart\Cart;
use App\Service\Cart\dto\CreateCartProduct;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use ApiPlatform\Core\Annotation\ApiResource;
use JetBrains\PhpStorm\Pure;
use App\Domain\Entity\Product\Product;


use App\Domain\Entity\Product\ProductPackage;
use App\Domain\Entity\ReferenceBook\ReferenceBookValue;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'controller' => CreateCartProductAction::class,
//            'denormalization_context' => ['groups' => ['Cart', 'Cart:write']],
        ],
    ],
    denormalizationContext: ['groups' => ['CartProduct']],
    normalizationContext: ['groups' => ['CartProduct', 'CartProduct:read', 'rbv:read']],
)]
#[Entity]
class CartProduct
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    #[Groups(['Cart:read', 'CartProduct:read'])]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Cart::class, inversedBy: 'cartProducts')]
    #[JoinColumn(name: 'cart_id', referencedColumnName: 'id')]
    #[Groups(['CartProduct'])]
    private Cart $cart;

    #[ManyToOne(targetEntity: Product::class)]
    #[JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['Cart', 'CartProduct'])]
    private ?Product $product = null;

    #[ManyToOne(targetEntity: ProductPackage::class)]
    #[JoinColumn(name: 'product_package_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['Cart', 'CartProduct'])]
    private ?ProductPackage $productPackage;

    #[Column(name: 'weight', type: 'decimal', scale: 4, nullable: true)]
    #[Groups(['CartProduct:read'])]
    private ?float $weight = 0.0;

    #[Column(name: 'volume', type: 'decimal', scale: 4, nullable: true)]
    #[Groups(['CartProduct:read'])]
    private ?float $volume = 0.0;

    #[ManyToOne(targetEntity: ReferenceBookValue::class)]
    #[Groups(['CartProduct:read'])]
    private ?ReferenceBookValue $rbvWeightMeasure;

    #[ManyToOne(targetEntity: ReferenceBookValue::class)]
    #[Groups(['CartProduct:read'])]
    private ?ReferenceBookValue $rbvVolumeMeasure;

    #[Column(name: 'quantity', type: 'integer')]
    #[Groups(['Cart', 'CartProduct'])]
    private int $quantity;

    #[Column(name: 'price', type: 'decimal', scale: 2)]
    #[Groups(['CartProduct:read'])]
    private float $price;

    #[Column(name: 'currency', type: 'string', length: 3)]
    #[Groups(['CartProduct:read'])]
    private string $currency = 'RUB';

    #[Column(name: 'discount', type: 'decimal', scale: 2, nullable: true)]
    #[Groups(['CartProduct:read'])]
    private ?float $discountPercent = null;

    #[Column(name: 'vat', type: 'decimal', scale: 2, nullable: true)]
    #[Groups(['CartProduct:read'])]
    private ?float $vat = 0.2;

    public function __construct(Product $product, int $quantity, ?ProductPackage $productPackage = null)
    {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->price = $product->getPrice();
        $this->vat = $product->getVat();
        $this->currency = $product->getCurrency() ?: $product->getShowcase()?->getCurrency();
        $this->productPackage = $productPackage;
        $this->weight = $product->getWeight();
        $this->volume = $product->getVolume();
        $this->rbvVolumeMeasure = $product->getRbvVolumeMeasure();
        $this->rbvWeightMeasure = $product->getRbvWeightMeasure();
    }

    public static function create(Cart $cart, Product $product, ?ProductPackage $productPackage, int $quantity): self
    {
        $self = new self($product, $quantity, $productPackage);
        $self->cart = $cart;
        return $self;
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getCart(): Cart
    {
        return $this->cart;
    }
    public function getProduct(): ?Product
    {
        return $this->product;
    }
    public function getProductPackage(): ?ProductPackage
    {
        return $this->productPackage;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    #[Pure] private function getProductPackageQuantity(): int
    {
        return $this->productPackage?->getQuantity() ?: 1;
    }

    #[Pure] public function getProductPackageCount(): int
    {
        return ceil($this->getQuantity() / $this->getProductPackageQuantity());
    }

    public function getPrice(): float
    {
        return $this->price;
    }
    public function getCurrency(): string
    {
        return $this->currency;
    }
    public function getDiscountPercent(): ?float
    {
        return $this->discountPercent;
    }

    #[Groups(['CartProduct:read'])]
    #[Pure] public function getVatAmount(): ?float
    {
        return $this->vat * $this->getTotalPriceWithDiscount();
    }

    public function getVat(): ?float
    {
        return $this->vat;
    }

    #[Pure] #[Groups(['CartProduct:read'])]
    public function hasChanges(): bool
    {
        return $this->getPrice() !== $this->product->getPrice() || !$this->product->isActive();
    }
    public function setProductPackage(?ProductPackage $productPackage): self
    {
        $this->productPackage = $productPackage;
        return $this;
    }
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }
    public function getRbvVolumeMeasure(): ?ReferenceBookValue
    {
        return $this->rbvVolumeMeasure;
    }
    public function getRbvWeightMeasure(): ?ReferenceBookValue
    {
        return $this->rbvWeightMeasure;
    }

    #[Groups(['CartProduct:read'])]
    public function getTotalWeight(): float
    {
        return $this->weight * $this->quantity;
    }

    #[Groups(['CartProduct:read'])]
    public function getTotalVolume(): float
    {
        return $this->volume * $this->quantity;
    }

    #[Groups(['CartProduct:read'])]
    public function getTotalPrice(): float
    {
        return $this->price * $this->quantity;
    }

    #[Groups(['CartProduct:read'])]
    public function getTotalPriceWithDiscount(): float
    {
        return round($this->price * $this->quantity * (100 - $this->discountPercent) / 100, 2);
    }

    #[Groups(['CartProduct:read'])]
    public function getDiscount(): float
    {
        return round($this->price * $this->quantity * $this->discountPercent / 100, 2);
    }

    public function getWeight(): ?float
    {
        return (float) $this->weight;
    }

    public function getVolume(): ?float
    {
        return (float) $this->volume;
    }

    /**
     * @param Cart $cart
     * @return CartProduct
     */
    public function setCart(Cart $cart): CartProduct
    {
        $this->cart = $cart;
        return $this;
    }

    /**
     * @param Product|null $product
     * @return CartProduct
     */
    public function setProduct(?Product $product): CartProduct
    {
        $this->product = $product;
        return $this;
    }
}
