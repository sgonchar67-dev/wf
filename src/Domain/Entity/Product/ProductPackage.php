<?php

namespace App\Domain\Entity\Product;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter as APFilter;
use App\Controller\Product\DeleteProductPackageAction;
use App\Domain\Entity\Product\PackType;
use App\Domain\Entity\Product\Product;
use App\Domain\Entity\ReferenceBook\ReferenceBookValue;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    itemOperations: [
        'get', 'put', 'patch', 
        'delete' => ['security' => "(is_granted('ROLE_OWNER') or (is_granted('ROLE_USER') and is_granted('PRODUCTS', object))) 
                and (object.getProduct().getCompany() == user.getEmployee().getCompany())",
            'controller' => DeleteProductPackageAction::class,
        ]
    ]
)]
#[ApiFilter(APFilter\NumericFilter::class, properties: ['product.id'])]
#[ApiFilter(APFilter\BooleanFilter::class, properties: ['archived'])]
#[ORM\Entity]
class ProductPackage
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[Groups(['Product', 'OrderProduct:read', 'CartProduct:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class, cascade: ['persist'], inversedBy: 'packages')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: PackType::class, cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['Product', 'CartProduct:read'])]
    private PackType $packType;

    #[ORM\Column(name: 'quantity', type: 'integer')]
    #[Groups(['Product', 'OrderProduct:read', 'CartProduct:read'])]
    private int $quantity;

    #[ORM\ManyToOne(targetEntity: ReferenceBookValue::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['Product', 'CartProduct:read'])]
    private ReferenceBookValue $rbvMeasure;

    #[ORM\Column(type: 'integer')]
    private int $barcode = 0;

    #[ORM\Column(name: 'weight', type: 'float', scale: 4)]
    #[Groups(['Product', 'OrderProduct:read', 'CartProduct:read'])]
    private float $weight = 0;

    #[ORM\ManyToOne(targetEntity: ReferenceBookValue::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['Product', 'CartProduct:read'])]
    private ?ReferenceBookValue $rbvWeightMeasure = null;

    #[ORM\Column(name: 'volume', type: 'float', scale: 4)]
    #[Groups(['Product', 'OrderProduct:read', 'CartProduct:read'])]
    private float $volume = 0;

    #[ORM\ManyToOne(targetEntity: ReferenceBookValue::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['Product', 'CartProduct:read'])]
    private ?ReferenceBookValue $rbvVolumeMeasure = null;

    #[ORM\Column(type: 'boolean')]
    private $archived = false;

    /**
     * @param Product $product
     * @param PackType $packType
     * @param int $quantity
     * @param \App\Domain\Entity\ReferenceBook\ReferenceBookValue $rbvMeasure
     * @param float|int $weight
     * @param ReferenceBookValue|null $rbvWeightMeasure
     * @param float|int $volume
     * @param ReferenceBookValue|null $rbvVolumeMeasure
     */
    public function __construct(Product $product, PackType $packType, int $quantity, ReferenceBookValue $rbvMeasure, float|int $weight, ?ReferenceBookValue $rbvWeightMeasure, float|int $volume, ?ReferenceBookValue $rbvVolumeMeasure)
    {
        $this->product = $product;
        $this->packType = $packType;
        $this->quantity = $quantity;
        $this->rbvMeasure = $rbvMeasure;
        $this->weight = $weight;
        $this->rbvWeightMeasure = $rbvWeightMeasure;
        $this->volume = $volume;
        $this->rbvVolumeMeasure = $rbvVolumeMeasure;
    }

    public static function create(
        Product             $product,
        PackType            $packType,
        int                 $quantity,
        ReferenceBookValue  $measure,
        float               $weight,
        ?ReferenceBookValue $rbvWeightMeasure,
        float               $volume,
        ?ReferenceBookValue $rbvVolumeMeasure
    ): self {
        return new self(
            $product,
            $packType,
            $quantity,
            $measure,
            $weight,
            $rbvWeightMeasure,
            $volume,
            $rbvVolumeMeasure
        );
    }

    public function __clone() {
        if ($this->id !== null) {
            $this->id = null;
        }
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    #[Groups(['OrderProduct:read'])]
    public function getName(): string
    {
        return $this->packType->getName();
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

    public function getRbvMeasure(): ReferenceBookValue
    {
        return $this->rbvMeasure;
    }

    #[Groups(['OrderProduct:read'])]
    public function getMeasure(): string
    {
        return $this->getRbvMeasure()->getValue();
    }

    public function getBarcode(): ?int
    {
        return $this->barcode;
    }

    public function setBarcode(int $barcode): self
    {
        $this->barcode = $barcode;
        return $this;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function getRbvWeightMeasure(): ?ReferenceBookValue
    {
        return $this->rbvWeightMeasure;
    }

    public function setRbvWeightMeasure(ReferenceBookValue $rbvWeightMeasure): self
    {
        $this->rbvWeightMeasure = $rbvWeightMeasure;
        return $this;
    }

    public function getVolume(): float
    {
        return $this->volume;
    }

    public function setVolume(float $volume): self
    {
        $this->volume = $volume;
        return $this;
    }

    public function getRbvVolumeMeasure(): ?ReferenceBookValue
    {
        return $this->rbvVolumeMeasure;
    }

    public function setRbvVolumeMeasure(ReferenceBookValue $rbvVolumeMeasure): self
    {
        $this->rbvVolumeMeasure = $rbvVolumeMeasure;
        return $this;
    }

    public function getPackType(): PackType
    {
        return $this->packType;
    }

    public function setPackType(PackType $packType): self
    {
        $this->packType = $packType;

        return $this;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }
}
