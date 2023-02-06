<?php

namespace App\Domain\Entity\Showcase;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ShowcaseCategory\GetShowcaseCategoryTreeAction;
use App\Domain\Entity\Showcase\Showcase;
use App\Domain\Entity\CollectionTrait;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\ResourceCategory;
use App\Domain\Entity\Product\Product;
use App\Domain\Entity\ReferenceBook\ReferenceBook;
use App\Repository\ShowcaseCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Mapping\Annotation as Gedmo;
use JetBrains\PhpStorm\Pure;

//use Gedmo\Mapping\Annotation\Slug;
//use Gedmo\Mapping\Annotation\Tree;
//use Gedmo\Mapping\Annotation\TreeLeft;
//use Gedmo\Mapping\Annotation\TreeRight;
//use Gedmo\Mapping\Annotation\TreeParent;
//use Gedmo\Mapping\Annotation\TreeRoot;
//use Gedmo\Mapping\Annotation\TreeLevel;

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
            'controller' => GetShowcaseCategoryTreeAction::class,
            'path' => '/showcase_categories/tree/{showcase_id}',
            'requirements' => ['showcase_id' => '\d+'],
            'openapi_context' => [
                'parameters' => [
                    [
                        "name" => "showcase_id",
                        'in' => "path",
                        "description" => "Showcase id",
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
#[UniqueConstraint(name: "IDX_SHOWCASE_SLUG", columns: ["showcase_id", "slug"])]
#[Entity(repositoryClass: ShowcaseCategoryRepository::class)]
class ShowcaseCategory
{
    use CollectionTrait;
    const ROOT_CATEGORY_NAME = 'Все товары';
    const ROOT_CATEGORY_DESCRIPTION = self::ROOT_CATEGORY_NAME;

    #[Column(type: 'integer')]
    #[Id]
    #[GeneratedValue]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Showcase::class, inversedBy: 'categories')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Showcase $showcase;

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
    #[Gedmo\TreeLeft()]
    private int $lft;

    /**
     * @Gedmo\TreeRight
     */
    #[Column(type: 'integer')]
    #[Gedmo\TreeRight()]
    private int $rgt;

    /**
     * @Gedmo\TreeParent
     */
    #[ManyToOne(targetEntity: 'ShowcaseCategory', inversedBy: 'children')]
    #[JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Gedmo\TreeParent()]
    private ?ShowcaseCategory $parent;

    /**
     * @Gedmo\TreeRoot
     */
    #[ManyToOne(targetEntity: 'ShowcaseCategory')]
    #[JoinColumn(name: 'root', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Gedmo\TreeRoot()]
    private ShowcaseCategory $root;

    /**
     * @Gedmo\TreeLevel
     */
    #[Column(name: 'lvl', type: 'integer')]
    #[Gedmo\TreeLevel()]
    private int $level;

    /**
     * @var Collection<int, ShowcaseCategory>
     */
    #[OneToMany(mappedBy: 'parent', targetEntity: 'ShowcaseCategory')]
    private Collection $children;

    /**
     * @var Collection<int, Product>
     */
    #[OneToMany(mappedBy: 'showcaseCategory', targetEntity: Product::class)]
    private Collection $products;

    /**
     * @var Collection<int, \App\Domain\Entity\ReferenceBook\ReferenceBook>
     */
    #[ManyToMany(targetEntity: ReferenceBook::class)]
    #[JoinTable(name: 'showcase_categories_reference_books', joinColumns: ['(name="showcase_category_id", referencedColumnName="id")'], inverseJoinColumns: ['(name="rb_id", referencedColumnName="id")'])]
    private Collection $referenceBooks;

    #[Pure] public function __construct(
        Showcase $showcase,
        string $title,
        ?string $description = null,
        ?ShowcaseCategory $parent = null
    ) {
        $this->children = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->referenceBooks = new ArrayCollection();
        $this->showcase = $showcase;
        $this->title = $title;
        $this->description = $description;
        $this->parent = $parent;
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
        return new self($company->getShowcase(), $title, $description);
    }

    #[Pure] public static function create(
        string $title,
        string $description,
        Showcase $showcase,
        ?ShowcaseCategory $parent = null
    ): self
    {
        return new self($showcase, $title, $description, $parent);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTitle($title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setDescription($description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setParent(?ShowcaseCategory $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function getRoot(): self
    {
        return $this->root;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function getLeft(): int
    {
        return $this->lft;
    }

    public function getRight(): int
    {
        return $this->rgt;
    }

    public function addReferenceBook(ReferenceBook $referenceBook)
    {
        $this->referenceBooks->add($referenceBook);
    }

    /**
     * @return Collection<int, \App\Domain\Entity\ReferenceBook\ReferenceBook>
     */
    public function getReferenceBooks(): Collection
    {
        return $this->referenceBooks;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }


    public function setProducts(array|Collection $products): ShowcaseCategory
    {
        $this->products = $this->createCollection($products);
        return $this;
    }

    public function getProductCount(): int
    {
        return $this->products->count();
    }

    public function getShowcase(): Showcase
    {
        return $this->showcase;
    }

    public function setShowcase(Showcase $showcase): ShowcaseCategory
    {
        $this->showcase = $showcase;
        return $this;
    }

    public function edit(string $title, string $description, ?ShowcaseCategory $parent): ShowcaseCategory
    {
        $this->title = $title;
        $this->description = $description;
        $this->parent = $parent;
        return $this;
    }
}
