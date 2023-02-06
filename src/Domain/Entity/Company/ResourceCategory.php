<?php //declare(strict_types=1);

namespace App\Domain\Entity\Company;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ResourceCategory\GetResourceCategoryTreeAction;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\CollectionTrait;
use App\Domain\Entity\Product\Product;
use App\Repository\ResourceCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Mapping\Annotation as Gedmo;
use JetBrains\PhpStorm\Pure;

/**
 * @Gedmo\Tree(type="nested")
 */
//#[Gedmo\Tree(data: ['type' => 'nested'])]
#[ApiResource(
    collectionOperations: [
        'get',
        'post',
        'get_tree' => [
            'pagination_enabled' => false,
            'method' => 'GET',
            'controller' => GetResourceCategoryTreeAction::class,
            'path' => '/resource_categories/tree/{company_id}',
            'requirements' => ['company_id' => '\d+'],
            'openapi_context' => [
                'parameters' => [
                    [
                        "name" => "company_id",
                        'in' => "path",
                        "description" => "Company id",
                        "required" => true,
                        'schema' => ['type' => 'integer'],
                        'style' => 'simple'
                    ]
                ]
            ],
            'defaults' => ['_api_receive' => false]
        ]
    ]
)]
#[UniqueConstraint(name: "IDX_COMPANY_SLUG", columns: ["company_id", "slug"])]
#[Entity(repositoryClass: ResourceCategoryRepository::class)]
class ResourceCategory
{
    use CollectionTrait;
    const ROOT_CATEGORY_NAME = 'Все товары';
    const ROOT_CATEGORY_DESCRIPTION = self::ROOT_CATEGORY_NAME;
    
    #[Column]
    #[Id]
    #[GeneratedValue]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Company::class, inversedBy: 'categories')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Company $company;

    #[Column(length: 64)]
    private string $title;

    #[Column(type: 'text', nullable: true)]
    private ?string $description;

    /**
     * @Gedmo\Slug(fields={"title"})
     */
    #[Gedmo\Slug(fields: ["title"])]
    #[Column(length: 64)]
    private string $slug;

    /**
     * @Gedmo\TreeLeft
     */
    #[Column(type: 'integer')]
    private int $lft;

    /**
     * @Gedmo\TreeRight
     */
    #[Column(type: 'integer')]
    private int $rgt;
    
    /**
     * @Gedmo\TreeParent
     */
    #[ManyToOne(targetEntity: 'ResourceCategory', inversedBy: 'children')]
    #[JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?ResourceCategory $parent;

    /**
     * @Gedmo\TreeRoot
     */
    #[ManyToOne(targetEntity: 'ResourceCategory')]
    #[JoinColumn(name: 'root', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ResourceCategory $root;

    /**
     * @Gedmo\TreeLevel
     */
    #[Column(name: 'lvl', type: 'integer')]
    private int $level;

    /**
     * @var Collection<int, ResourceCategory>
     */
    #[OneToMany(mappedBy: 'parent', targetEntity: 'ResourceCategory')]
    private Collection $children;

    /**
     * @var Collection<int, Product>
     */
    #[OneToMany(mappedBy: 'resourceCategory', targetEntity: Product::class)]
    private Collection $products;


    #[Pure] public function __construct(
        Company $company,
        string $title,
        ?string $description = null,
        ?ResourceCategory $parent = null
    ) {
        $this->children = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->company = $company;
        $this->title = $title;
        $this->description = $description;
        $this->parent = $parent;
    }


    #[Pure] public static function create(
        string            $title,
        string            $description,
        Company           $company,
        ?ResourceCategory $parent = null
    ): self
    {
        return new self($company, $title, $description, $parent);
    }

    #[Pure] public function __toString()
    {
        return $this->title;
    }

    #[Pure] public static function createRoot(
        Company $company,
        string $title = self::ROOT_CATEGORY_NAME,
        string $description = self::ROOT_CATEGORY_DESCRIPTION,
    ): self {
        return new self($company, $title, $description);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getLft(): int
    {
        return $this->lft;
    }

    public function setLft(int $lft): self
    {
        $this->lft = $lft;
        return $this;
    }

    public function getRgt(): int
    {
        return $this->rgt;
    }

    public function setRgt(int $rgt): self
    {
        $this->rgt = $rgt;
        return $this;
    }

    public function getParent(): ?ResourceCategory
    {
        return $this->parent;
    }

    public function setParent(?ResourceCategory $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    public function getRoot(): self
    {
        return $this->root;
    }

    public function setRoot(ResourceCategory $root): self
    {
        $this->root = $root;
        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return Collection<int, ResourceCategory>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param Collection|ResourceCategory[] $children
     */
    public function setChildren(Collection|array $children): self
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @return Collection<int, \App\Domain\Entity\Product\Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function setProducts(array|Collection $products): ResourceCategory
    {
        $this->products = $this->createCollection($products);
        return $this;
    }

    public function getProductCount(): int
    {
        return $this->products->count();
    }

    public function edit(string $title, string $description, ?ResourceCategory $parent): self
    {
        $this->title = $title;
        $this->description = $description;
        $this->parent = $parent;
        return $this;
    }
}