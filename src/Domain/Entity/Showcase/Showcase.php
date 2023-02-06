<?php

namespace App\Domain\Entity\Showcase;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Domain\Entity\Company\ResourceCategory;
use App\Domain\Entity\Showcase\ShowcaseCategory;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Contractor\ContractorGroup;
use App\Domain\Entity\Image;
use App\Domain\Entity\Product\Product;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToMany;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Delivery\Delivery;
use App\Domain\Entity\Payment\PaymentMethod;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Entity]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => ['security' => "is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')"]
    ],
    itemOperations: [
        'get',
        'put' => ['security' => "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getCompany().getUser() == user)"],
        'patch' => ['security' => "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getCompany().getUser() == user)"],
        'delete' => ['security' => "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getCompany().getUser() == user)"],
    ],
    denormalizationContext: ['groups' => ['Showcase', 'Showcase:write']],
    normalizationContext: ['groups' => ['Showcase', 'Showcase:read']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'domain' => 'exact',
])]
class Showcase
{
    #[Id]
    #[Column(options: ['unsigned' => true])]
    #[GeneratedValue]
    #[Groups(['Showcase:read'])]
    private ?int $id = null;

    #[OneToOne(inversedBy: 'showcase', targetEntity: Company::class)]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['Showcase'])]
    private Company $company;

    #[Column]
    #[Groups(['Showcase'])]
    private string $name;

    #[Column(type: "text", nullable: true)]
    #[Groups(['Showcase'])]
    public ?string $description = null;

    #[Column(name: 'domain', type: 'string', nullable: true)]
    #[Groups(['Showcase'])]
    private ?string $domain = null;

    #[OneToOne(mappedBy: 'showcase', targetEntity: SSLCertificate::class)]
    #[Groups(['Showcase:read'])]
    #[ApiSubresource(maxDepth: 1)]
    private ?SSLCertificate $sslCertificate = null;

    #[Column(name: 'price_currency', type: 'string', length: 3, options: ['default' => 'RUB'])]
    #[Groups(['Showcase'])]
    private string $currency = 'RUB';

    #[Column(options: ['default' => false])]
    #[Groups(['Showcase'])]
    #[ApiProperty(readable: true)]
    private bool $isPublished = false;

    #[Column(options: ['default' => false])]
    #[Groups(['Showcase'])]
    #[ApiProperty(readable: true)]
    private bool $isIndexingEnabled = false;

    /**
     * showcase is available to only contractor groups
     */
    #[Column(options: ['default' => false])]
    #[Groups(['Showcase'])]
    #[ApiProperty(readable: true)]
    private bool $isPrivate = false;

    #[Column]
    #[Groups(['Showcase:read'])]
    private \DateTime $createdAt;

    #[OneToOne(targetEntity: Employee::class)]
    #[JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['Showcase'])]
    private ?Employee $manager = null;

    /**
     * @var Collection<int, ShowcaseCategory>
     */
    #[OneToMany(mappedBy: 'showcase', targetEntity: ShowcaseCategory::class, cascade: ['persist', 'remove'])]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Showcase:read'])]
    private Collection $categories;


    /**
    * @var Collection<int, Product>
    */
    #[OneToMany(mappedBy: 'showcase', targetEntity: Product::class)]
    #[Groups(['Showcase:read'])]
    #[ApiSubresource(maxDepth: 1)]
    private Collection $products;

    /**
     * @var Collection<int, \App\Domain\Entity\Payment\PaymentMethod>
     */
    #[OneToMany(mappedBy: 'showcase', targetEntity: PaymentMethod::class, cascade: ['persist', 'remove'])]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Showcase:read'])]
    private Collection $paymentMethods;

    /**
     * @var Collection<int, Delivery>
     */
    #[OneToMany(mappedBy: 'showcase', targetEntity: Delivery::class, cascade: ['persist', 'remove'])]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Showcase:read'])]
    private Collection $deliveries;

    #[OneToOne(targetEntity: Image::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    #[JoinColumn(nullable: true)]
    #[ApiProperty(iri: 'http://schema.org/banner')]
    #[Groups(['Showcase'])]
    private ?Image $banner = null;

    /**
     * @todo
     * @var Collection<int, ContractorGroup>
     */
    private Collection $privateContractorGroups;

    public function __construct(Company $company)
    {
        $this->company = $company;
        $this->products = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->deliveries = new ArrayCollection();
        $this->paymentMethods = new ArrayCollection();
        $this->privateContractorGroups = new ArrayCollection();

        $this->createdAt = new \DateTime();
    }

    public static function create(Company $company): self
    {
        return new Showcase($company);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return Collection<int, Delivery>
     */
    public function getDeliveries(): Collection
    {
        return $this->deliveries;
    }

    /**
     * @param Collection|Delivery[] $deliveries
     */
    public function addDeliveries(array $deliveries): self
    {
        foreach ($deliveries as $delivery) {
            $delivery->setShowcase($this);
            $this->deliveries->add($delivery);
        }
        return $this;
    }

    public function addDelivery(Delivery $delivery): self
    {
        $delivery->setShowcase($this);
        $this->deliveries->add($delivery);
        return $this;
    }


    public function addCategory(ShowcaseCategory $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }
        return $this;
    }

    public function removeCategory(ShowcaseCategory $resourceCategory): self
    {
        $this->categories->removeElement($resourceCategory);
        return $this;
    }


    /**
     * @param Collection|Delivery[] $deliveries
     */
    public function setDeliveries(Collection|array $deliveries): self
    {
        $this->deliveries->clear();
        $this->addDeliveries($deliveries);
        return $this;
    }

    /**
     * @return Collection<int, \App\Domain\Entity\Payment\PaymentMethod>
     */
    public function getPaymentMethods(): Collection
    {
        return $this->paymentMethods;
    }

    public function addPayment(PaymentMethod $payment): self
    {
        if (!$this->paymentMethods->contains($payment)) {
            $payment->setShowcase($this);
            $this->paymentMethods->add($payment);
        }
        return $this;
    }

    /**
     * @param Collection|\App\Domain\Entity\Payment\PaymentMethod[] $paymentMethods
     */
    public function setPaymentMethods(Collection|array $paymentMethods): self
    {
        $this->paymentMethods->clear();
        foreach ($paymentMethods as $payment) {
            $this->addPayment($payment);
        }
        return $this;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getSslCertificate(): ?SSLCertificate
    {
        return $this->sslCertificate;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Showcase
     */
    public function setDescription(?string $description): Showcase
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return bool
     */
    public function isIndexingEnabled(): bool
    {
        return $this->isIndexingEnabled;
    }

    /**
     * @param bool $isIndexingEnabled
     * @return Showcase
     */
    public function setIsIndexingEnabled(bool $isIndexingEnabled): Showcase
    {
        $this->isIndexingEnabled = $isIndexingEnabled;
        return $this;
    }

    /**
     * @return \App\Domain\Entity\Company\Employee|null
     */
    public function getManager(): ?Employee
    {
        return $this->manager;
    }

    /**
     * @param \App\Domain\Entity\Company\Employee|null $manager
     * @return Showcase
     */
    public function setManager(?Employee $manager): Showcase
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * @param string $name
     * @return Showcase
     */
    public function setName(string $name): Showcase
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string|null $domain
     * @return Showcase
     */
    public function setDomain(?string $domain): Showcase
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @param SSLCertificate|null $sslCertificate
     * @return Showcase
     */
    public function setSslCertificate(?SSLCertificate $sslCertificate): Showcase
    {
        $this->sslCertificate = $sslCertificate;
        return $this;
    }

    /**
     * @param string $currency
     * @return Showcase
     */
    public function setCurrency(string $currency): Showcase
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param bool $isPublished
     * @return Showcase
     */
    public function setIsPublished(bool $isPublished): Showcase
    {
        $this->isPublished = $isPublished;
        return $this;
    }

    /**
     * @param bool $isPrivate
     * @return Showcase
     */
    public function setIsPrivate(bool $isPrivate): Showcase
    {
        $this->isPrivate = $isPrivate;
        return $this;
    }

    public function getBanner(): ?Image
    {
        return $this->banner;
    }

    public function setBanner(?Image $banner): self
    {
        $this->banner = $banner;

        return $this;
    }

    public function hasCategory(?ShowcaseCategory $category): bool
    {
        return $this->categories->contains($category);
    }

    public function getRootCategory(): ?ShowcaseCategory
    {
        $root = null;
        /** @var ShowcaseCategory|null $first */
        if ($first = $this->categories->first()) {
            $root = $first->getRoot();
        }
        return $root;
    }

    #[Groups(['Showcase:read'])]
    public function getProductsCount(): int
    {
        return $this->products->count();
    }
}
