<?php

namespace App\Domain\Entity\Product;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter as APFilter;
use App\Controller\Product\CopyProductAction;
use App\Controller\Product\CreateProductAction;
use App\Domain\Entity\CollectionTrait;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\ResourceCategory;
use App\Domain\Entity\Contractor\Contractor;
use App\Domain\Entity\Contractor\ContractorGroup;
use App\Domain\Entity\Document;
use App\Domain\Entity\Image;
use App\Domain\Entity\ReferenceBook\ReferenceBookValue;
use App\Domain\Entity\Showcase\Showcase;
use App\Domain\Entity\Showcase\ShowcaseCategory;
use App\Domain\ValueObject\Price;
use App\Filter\TreeFilter;
use App\Helper\Doctrine\CollectionHelper;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'security' => "is_granted('ROLE_USER')",
            'controller' => CreateProductAction::class,
        ]
    ],
    itemOperations: [
        'get', 'put', 'patch', 'delete',
        'copy' => [
            'method' => 'POST',
            'controller' => CopyProductAction::class,
            'path' => '/products/{id}/copy',
            'requirements' => ['id' => '\d+'],
            'defaults' => ['_api_receive' => false],
            'openapi_context' => [
                'summary' => "Copy product {id} in new record.",
                'requestBody' => [
                    'content' => []
                ]
            ]
        ]
    ],
    attributes: ["pagination_client_items_per_page" => true],
    denormalizationContext: ['groups' => ['Product',  'Product:write',  'Product:update',  'rbv']],
//    normalizationContext: ['groups' => ['Product', 'Product:read', 'rbv:read']],
)]
#[ApiFilter(APFilter\BooleanFilter::class, properties: ['archived'])]
#[ApiFilter(APFilter\DateFilter::class, properties: ['updatedAt'])]
#[ApiFilter(APFilter\ExistsFilter::class, properties: ['showcase', 'images'])]
#[ApiFilter(APFilter\NumericFilter::class, properties: ['showcase.id', 'company.id'])]
#[ApiFilter(APFilter\OrderFilter::class, properties: ['price', 'createdAt', 'name', 'hasDiscount', 'updatedAt', 'showcaseCategory.title', 'stockBalance'])]
#[ApiFilter(TreeFilter::class, properties: ['showcaseCategory.id', 'resourceCategory.id'])]
#[ORM\Entity]
class Product
{
    private const VAT_0_PERCENT = 0;
    private const VAT_10_PERCENT = 0.1;
    private const VAT_18_PERCENT = 0.18;
    private const VAT_20_PERCENT = 0.2;
    private const AVAILABLE_VATS = [
        self::VAT_0_PERCENT,
        self::VAT_10_PERCENT,
        self::VAT_18_PERCENT,
        self::VAT_20_PERCENT,
    ];

    use CollectionTrait;

    /** @var int шт. */
    public const RBV_MEASURE_ID_DEFAULT = 19;
    public const RBV_VOLUME_MEASURE_ID_DEFAULT = 9;
    public const RBV_WEIGHT_MEASURE_ID_DEFAULT = 14;
    /** @var int Товар для продажи */
    public const RESOURCE_TYPE_DEFAULT = 5;

    #[ORM\Id]
    #[ORM\Column(options: ['unsigned' => true])]
    #[ORM\GeneratedValue]
    #[Groups(['Product:read', 'CartProduct:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Showcase::class, inversedBy: 'products')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['Product'])]
    private ?Showcase $showcase = null;

    #[ORM\Column(options: ['default' => false, 'comment' => 'В архиве'])]
    #[Groups(['Product'])]
    private bool $archived = false;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Product'])]
    private Company $company;

    #[ApiFilter(APFilter\SearchFilter::class, properties: ['name' => 'partial'])]
    #[ORM\Column]
    #[Groups(['Product', 'CartProduct:read'])]
    private string $name;

    #[ORM\Column(name: 'description', type: 'text', nullable: true, options: ['comment' => 'Описание'])]
    #[Groups(['Product'])]
    private ?string $description = null;

    #[ApiFilter(APFilter\RangeFilter::class, properties: ['price'])]
    #[ORM\Column(name: 'price', type: 'decimal', precision: 20, scale: 2, options: ['comment' => 'Цена'])]
    #[Groups(['Product', 'CartProduct:read'])]
    private float $price = 0;

    #[ORM\Column(name: 'cost_price', type: 'decimal', precision: 20, scale: 2, nullable: true, options: ['comment' => 'Себестоимость'])]
    #[Groups(['Product'])]
    private ?float $costPrice = null;
    
    #[ORM\Column(name: 'min_price', type: 'decimal', precision: 20, scale: 2, nullable: true, options: ['comment' => 'Цена минимальная'])]
    #[Groups(['Product'])]
    private ?float $minPrice = null;

    #[ORM\Column(name: 'currency', type: 'string', length: 3, options: ['comment' => 'Валюта'])]
    #[Groups(['Product:read', 'CartProduct:read'])]
    private string $currency = 'RUB';

    /**
     * @var bool only contractors groups access
     */
    #[ORM\Column(name: 'is_private', options: ['default' => false, 'comment' => 'Товар доступен только для определенных групп контрагентов'])]
    #[Groups(['Product'])]
    private bool $private = false;

    #[ORM\Column(name: 'has_discount', type: 'boolean', options: ['default' => false, 'comment' => 'Есть скидка'])]
    #[Groups(['Product'])]
    private bool $hasDiscount = false;

    #[ORM\Column(name: 'show_discount', type: 'boolean', options: ['default' => true, 'comment' => 'Показывать скидку'])]
    #[Groups(['Product'])]
    private bool $showDiscount = true;

    #[ORM\Column(name: 'article', type: 'string', length: 100, nullable: true, options: ['comment' => 'Артикул'])]
    #[Groups(['Product', 'CartProduct:read'])]
    private ?string $article = null;

    #[ORM\Column(name: 'min_balance', type: 'integer', nullable: true, options: ['comment' => 'Остаток неснижаемый'])]
    #[Groups(['Product'])]
    private ?int $minBalance = null;

    #[ORM\Column(name: 'stock_balance', type: 'integer', nullable: true, options: ['comment' => 'Остаток на складе'])]
    #[Groups(['Product'])]
    private ?int $stockBalance = null;

    #[ORM\Column(name: 'need_show_stock_balance', type: 'boolean', options: ['default' => false, 'comment' => 'Показывать остаток на складе'])]
    #[Groups(['Product'])]
    private bool $needShowStockBalance = false;

    #[ORM\Column(name: 'preorder', type: 'boolean', options: ['default' => false, 'comment' => 'Предзаказ'])]
    #[Groups(['Product'])]
    private bool $preorder = false;

    #[ORM\ManyToOne(targetEntity: ResourceCategory::class, cascade: ['persist'], inversedBy: 'products')]
    #[Groups(['Product'])]
    private ?ResourceCategory $resourceCategory = null;

    #[ORM\ManyToOne(targetEntity: ShowcaseCategory::class, inversedBy: 'products')]
    #[Groups(['Product'])]
    private ?ShowcaseCategory $showcaseCategory = null;

    /** НДС */
    #[ORM\Column(name: 'vat',  type: 'decimal', scale: 2, options: ['default' => '0.00', 'comment' => 'НДС'])]
    #[Groups(['Product'])]
    private float $vat = 0.00;

    #[ORM\ManyToOne(targetEntity: Vendor::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['Product'])]
    private ?Vendor $vendor = null;

    #[ORM\ManyToOne(targetEntity: Contractor::class)]
    #[ORM\JoinColumn(name: 'supplier_id', referencedColumnName: 'id', onDelete: "SET NULL")]
    #[Groups(['Product'])]
    private ?Contractor $supplier = null;

    #[ORM\Column(name: 'weight', type: 'decimal', precision: 20, scale: 4, nullable: true, options: ['comment' => 'Вес'])]
    #[Groups(['Product', 'CartProduct:read'])]
    private ?float $weight = null;

    #[ORM\Column(name: 'volume', type: 'decimal', precision: 20, scale: 4, nullable: true, options: ['comment' => 'Объем'])]
    #[Groups(['Product', 'CartProduct:read'])]
    private ?float $volume = null;

    /** default 19 */
    #[ORM\ManyToOne(targetEntity: ReferenceBookValue::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['Product', 'CartProduct:read'])]
    private ReferenceBookValue $rbvMeasure;

    /** default 14 */
    #[ORM\ManyToOne(targetEntity: ReferenceBookValue::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['Product', 'CartProduct:read'])]
    private ReferenceBookValue $rbvWeightMeasure;

    /** default 9 */
    #[ORM\ManyToOne(targetEntity: ReferenceBookValue::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['Product', 'CartProduct:read'])]
    private ReferenceBookValue $rbvVolumeMeasure;

    #[ORM\Column(name: 'code', type: 'string', nullable: true, options: ['comment' => 'Код'])]
    #[Groups(['Product', 'CartProduct:read'])]
    private ?string $code = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', options: ['comment' => 'Дата создания'])]
    #[Groups(['Product'])]
    private DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', options: ['comment' => 'Дата обновления'])]
    #[Groups(['Product'])]
    private DateTimeInterface $updatedAt;


    /**
     * @var Collection<int, Property>
     */
    #[ORM\ManyToMany(targetEntity: Property::class)]
    #[ORM\JoinTable(name: 'products_properties')]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Product:update'])]
    private Collection $properties;

    /**
     * @var Collection<int, ProductPackage>
     */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductPackage::class, cascade: ['persist'])]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Product:update', 'CartProduct:read'])]
    private Collection $packages;

    /**
     * @var Collection<int, ProductPrice>
     */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductPrice::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['Product:update'])]
    private Collection $prices;

    /**
     * @var Collection<int, Document>
     */
    #[ORM\ManyToMany(targetEntity: Document::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    #[Groups(['Product:update'])]
    private Collection $documents;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\ManyToMany(targetEntity: Product::class)]
    #[ORM\JoinTable(name: 'recommendation_products')]
    #[ORM\JoinColumn(name: "product_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "recommendation_product_id", referencedColumnName: "id")]
    #[Groups(['Product:update'])]
    private Collection $recommendedProducts;

    /**
     * @var Collection<int, Contractor>
     */
    #[ORM\ManyToMany(targetEntity: Contractor::class)]
    #[ORM\JoinTable(name: 'products_contractors')]
    #[ORM\JoinColumn(name: "product_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "contractor_id", referencedColumnName: "id")]
    #[Groups(['Product:update'])]
    private Collection $contractors;

    /**
     * @var Collection<int, ContractorGroup>
     */
    #[ORM\ManyToMany(targetEntity: ContractorGroup::class)]
    #[ORM\JoinTable(name: 'products_contractor_groups')]
    #[ORM\JoinColumn(name: "product_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "contractors_group_id", referencedColumnName: "id")]
    #[Groups(['Product:update'])]
    private Collection $contractorGroups;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\ManyToMany(targetEntity: Image::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    #[Groups(['Product:update'])]
    private Collection $images;

    #[ORM\OneToOne(targetEntity: Image::class, cascade: ['persist', 'remove'])]
    #[Groups(['Product'])]
    private ?Image $imageMain = null;

    /**
     * @var Collection<int, ProductTag>
     */
    #[ORM\ManyToMany(targetEntity: ProductTag::class)]
    #[ORM\JoinTable(name: 'products_tags')]
    #[ORM\JoinColumn(name: "product_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "tag_id", referencedColumnName: "id")]
    #[Groups(['Product:update'])]
    private Collection $tags;


    /** @var Product|null @deprecated todo create separate entity modification */
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'modifications')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true)]
    private ?Product $parent = null;

    /**
     * @todo create separate entity modification
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Product::class)]
    private Collection $modifications;

    public function __construct(
        string             $name,
        Company            $company,
        ReferenceBookValue $rbvMeasure,
        ReferenceBookValue $rbvWeightMeasure,
        ReferenceBookValue $rbvVolumeMeasure,
    ) {
        $this->name = $name;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->properties = new ArrayCollection();
        $this->packages = new ArrayCollection();
        $this->prices = new ArrayCollection();
        $this->contractors = new ArrayCollection();
        $this->recommendedProducts = new ArrayCollection();
        $this->contractorGroups = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->company = $company;
        $this->rbvMeasure = $rbvMeasure;
        $this->rbvWeightMeasure = $rbvWeightMeasure;
        $this->rbvVolumeMeasure = $rbvVolumeMeasure;
    }

    public function __clone() {
        if ($this->id !== null) {
            $this->id = null;
            $this->createdAt = new \DateTime();
            $this->updatedAt = new \DateTime();
            $this->packages = $this->cloneRelationOneToMany($this->getPackages());
            $this->prices = $this->cloneRelationOneToMany($this->getPrices());
            $this->images = new ArrayCollection();
            $this->documents = new ArrayCollection();            
        }
    }

    private function cloneRelationOneToMany(Collection|array $relationOneToMany): Collection
    {
        $newRelations = new ArrayCollection();
        foreach ($relationOneToMany as $item) {
            $itemClone = clone $item;
            $itemClone->setProduct($this);
            $newRelations->add($itemClone);
        }
        return $newRelations;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getContractorPrice(Contractor $contractor): float
    {
        $discount = 0;
        if ($this->getContractorGroups()->contains($contractor->getGroup())) {
            $discount = $contractor->getGroup()?->getDiscount();
        }

        $price = new Price($this->currency, $this->price);
        return $price->getPriceWithDiscount($discount);
    }


    public function getContractorDiscountPercent(Contractor $contractor): ?float
    {
        return $this->getContractorGroups()->contains($contractor->getGroup())
            ? $contractor->getGroup()?->getDiscount()
            : null;
    }

    public function getContractorDiscount(Contractor $contractor): ?float
    {
        $price = new Price($this->currency, $this->price);
        return $this->getContractorGroups()->contains($contractor->getGroup())
            ? $price->getDiscount($contractor->getGroup()?->getDiscount())
            : null;
    }

    /**
     * @param Collection|ProductPrice[] $prices
     */
    public function setPrices(array|Collection $prices = []): self
    {
        $this->prices = $this->createCollection($prices);
        return $this;
    }

    /**
     * @return Collection<int, ProductPrice>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getModifications(): Collection
    {
        return $this->modifications;
    }

    public function getCostPrice(): ?float
    {
        return $this->costPrice;
    }

    public function getMinPrice(): ?float
    {
        return $this->minPrice;
    }

    public function isShowDiscount(): bool
    {
        return $this->showDiscount;
    }

    public function getMinBalance(): ?int
    {
        return $this->minBalance;
    }

    public function getStockBalance(): ?int
    {
        return $this->stockBalance;
    }

    public function isNeedShowStockBalance(): bool
    {
        return $this->needShowStockBalance;
    }

    public function isPreorder(): bool
    {
        return $this->preorder;
    }

    public function getResourceCategory(): ?ResourceCategory
    {
        return $this->resourceCategory;
    }

    public function getShowcaseCategory(): ?ShowcaseCategory
    {
        return $this->showcaseCategory;
    }

    #[Pure] public function getShowcaseCategoryName(): ?string
    {
        return $this->getShowcaseCategory()?->getTitle();
    }

    public function getVendor(): ?Vendor
    {
        return $this->vendor;
    }

    /**
     * @param Collection|Product[] $modifications
     * @return Product
     */
    public function setModifications(Collection|array $modifications = []): Product
    {
        $this->modifications = $this->createCollection($modifications);
        return $this;
    }

    /**
     * @param Vendor|null $vendor
     * @return Product
     */
    public function setVendor(?Vendor $vendor): Product
    {
        $this->vendor = $vendor;
        return $this;
    }

    public function getSupplier(): ?Contractor
    {
        return $this->supplier;
    }

    /**
     * @return Collection<int, Property>
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function getRbvWeightMeasure(): ?ReferenceBookValue
    {
        return $this->rbvWeightMeasure;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function getRbvVolumeMeasure(): ?ReferenceBookValue
    {
        return $this->rbvVolumeMeasure;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function getRbvMeasure(): ?ReferenceBookValue
    {
        return $this->rbvMeasure;
    }

    /**
     * @return Collection<int, ProductTag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function hasDiscount(): bool
    {
        return $this->hasDiscount;
    }

    public function getArticle(): ?string
    {
        return $this->article;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function archive(): self
    {
        $this->archived = true;
        return $this;
    }

    public function extractFromArchive(): self
    {
        $this->archived = false;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setCostPrice(float $costPrice): self
    {
        $this->costPrice = $costPrice;
        return $this;
    }

    public function setMinPrice(float $minPrice): self
    {
        $this->minPrice = $minPrice;
        return $this;
    }

    public function setRbvMeasure(ReferenceBookValue $rbvMeasure): self
    {
        $this->rbvMeasure = $rbvMeasure;
        return $this;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param ProductTag[]|Collection $tags
     * @return $this
     */
    public function setTags(array|Collection $tags): self
    {
        $this->tags = $this->createCollection($tags);
        return $this;
    }

    public function setHasDiscount(bool $hasDiscount): self
    {
        $this->hasDiscount = $hasDiscount;
        return $this;
    }

    public function setShowDiscount(bool $showDiscount): self
    {
        $this->showDiscount = $showDiscount;
        return $this;
    }

    public function setArticle(?string $article): self
    {
        $this->article = $article;
        return $this;
    }

    public function setMinBalance(int $minBalance): self
    {
        $this->minBalance = $minBalance;
        return $this;
    }

    public function setStockBalance(int $stockBalance): self
    {
        $this->stockBalance = $stockBalance;
        return $this;
    }

    public function setNeedShowStockBalance(bool $needShowStockBalance): self
    {
        $this->needShowStockBalance = $needShowStockBalance;
        return $this;
    }

    public function setPreorder(bool $preorder): self
    {
        $this->preorder = $preorder;
        return $this;
    }

    public function setResourceCategory(?ResourceCategory $resourceCategory): self
    {
        $this->resourceCategory = $resourceCategory;
        return $this;
    }

    public function setShowcaseCategory(?ShowcaseCategory $showcaseCategory): self
    {
        $this->showcaseCategory = $showcaseCategory;
        return $this;
    }

    public function setVat(float $vat): self
    {
        if (!in_array($vat, self::AVAILABLE_VATS)) {
            throw new \DomainException('Invalid vat');
        }
        $this->vat = $vat;
        return $this;
    }

    public function setSupplier(?Contractor $supplier): self
    {
        $this->supplier = $supplier;
        return $this;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function setVolume(float $volume): self
    {
        $this->volume = $volume;
        return $this;
    }

    public function setRbvWeightMeasure(ReferenceBookValue $rbvWeightMeasure): self
    {
        $this->rbvWeightMeasure = $rbvWeightMeasure;
        return $this;
    }

    public function setRbvVolumeMeasure(ReferenceBookValue $rbvVolumeMeasure): self
    {
        $this->rbvVolumeMeasure = $rbvVolumeMeasure;
        return $this;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param Collection|\App\Domain\Entity\Contractor\Contractor[] $contractors
     */
    public function setContractors(Collection|array $contractors): self
    {
        $this->contractors = $this->createCollection($contractors);
        return $this;
    }

    public function isPublished(): bool
    {
        return (bool) $this->showcase;
    }

    /**
     * @return Collection<int, \App\Domain\Entity\Contractor\ContractorGroup>
     */
    public function getContractorGroups(): Collection
    {
        return $this->contractorGroups;
    }

    /**
     * @return Collection<int, ProductPackage>
     */
    public function getPackages(): Collection
    {
        return $this->packages;
    }

    /**
     * @param Collection|ProductPackage[] $packages
     */
    public function setPackages(Collection|array $packages): self
    {
        $this->packages = $this->createCollection($packages);
        return $this;
    }

    public function addPackage(ProductPackage $package): self
    {
        $this->packages->add($package);
        return $this;
    }

    public function getVat(): float
    {
        return $this->vat;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return Collection<int, Contractor>
     */
    public function getContractors(): Collection
    {
        return $this->contractors;
    }

    /**
     * @param Collection|Property[] $properties
     */
    public function setProperties(array|Collection $properties): self
    {
        $this->properties = $this->createCollection($properties);
        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        $this->documents->removeElement($document);

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getRecommendedProducts(): Collection
    {
        return $this->recommendedProducts;
    }

    /**
     * @param Collection|Product[] $recommendedProducts
     */
    public function setRecommendedProducts(array|Collection $recommendedProducts): self
    {
        $this->recommendedProducts = CollectionHelper::create($recommendedProducts);

        return $this;
    }

    /**
     * @param Collection|ContractorGroup[] $contractorGroups
     */
    public function setContractorGroups(array|Collection $contractorGroups): self
    {
        $this->contractorGroups = CollectionHelper::create($contractorGroups);
        return $this;
    }

    public function setShowcase(?Showcase $showcase): self
    {
        $this->showcase = $showcase;
        return $this;
    }

    public function getShowcase(): ?Showcase
    {
        return $this->showcase;
    }

    public function createModification(string $caption, string $code): self
    {
        $modification = clone $this;
        $modification->parent = $this;
        $modification->id = null;
        $modification->name = $caption;
        $modification->code = $code;

        return $modification;
    }

    public function isActive(): bool
    {
        return !$this->archived;
    }

    public function publishOnShowcase(Showcase $showcase, ?ShowcaseCategory $category = null): static
    {
        if ($this->getCompany() !== $showcase->getCompany()) {
            throw new \DomainException(
                "The product {$this->id} does not belong to the showcase {$showcase->getId()}"
            );
        }
        $this->showcase = $showcase;
        $this->currency = $showcase->getCurrency();
        if ($this->showcase->hasCategory($category)) {
            $this->showcaseCategory = $category;
        } else {
            $this->showcaseCategory = $showcase->getRootCategory();
        }

        return $this;
    }

    public function removeFromShowcase(): static
    {
        $this->showcase = null;
        $this->showcaseCategory = null;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getTagNames(): array
    {
        return $this->getTags()->map(fn(ProductTag $t) => $t->getValue())->toArray();
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function hasImages(): bool
    {
        return (bool) $this->images->count();
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
        }
        return $this;
    }

    public function removeImage(Image $image): self
    {
        $this->images->removeElement($image);
        return $this;
    }

    public function getImageMain(): ?Image
    {
        return $this->imageMain;
    }

    public function setImageMain(?Image $imageMain): self
    {
        if ($imageMain !== null && !$this->getImages()->contains($imageMain)) {
            $this->addImage($imageMain);
        }
        $this->imageMain = $imageMain;

        return $this;
    }

    public function isPrivate(): bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): self
    {
        $this->private = $private;
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
