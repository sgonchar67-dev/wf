<?php

namespace App\Domain\Entity\ReferenceBook;

use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Domain\Entity\CollectionTrait;
use App\Domain\Entity\Company\Company;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[Entity]
#[ApiResource(
    denormalizationContext: ['groups' => ['ReferenceBook', 'ReferenceBook:write']],
    normalizationContext: ['groups' => ['ReferenceBook', 'ReferenceBook:read',]],
)]
class ReferenceBook
{
    use CollectionTrait;
    /** @deprecated */
    public const RB_SYSTEM_TYPE_SYSTEM = 1; //системный справочник
    /** @deprecated */
    public const RB_SYSTEM_TYPE_CUSTOM = 2; //пользовательский справочник

    /** @var int тип справочника - числовой */
    public const TYPE_RB_NUMBER = 1;
    /** @var int тип справочника - текстовый */
    public const TYPE_RB_TEXT = 2;
    /** @var int тип справочника - дата */
    public const TYPE_RB_DATE = 3;

    public const RB_ID_DELIVERY = 1; //ID справочника со способами доставки
    public const RB_ID_PAYMENT = 2; //ID справочника со способами оплаты
    /** @deprecated  */
    public const RB_ID_ARTICLE = 3; //ID справочника с артикулами
    public const RB_ID_MEASURE = 4; //ID справочника с единицами измерения
    public const RB_ID_WEIGHT = 5; //ID справочника с весом
    public const RB_ID_VOLUME = 6; //ID справочника с объемом
    public const RB_ID_PACK_RB = 7; //ID справочника с видов упаковки
    /** @deprecated  */
    public const RB_ID_VENDOR = 10;

    #[Id]
    #[Column(options: ['unsigned' => true])]
    #[GeneratedValue]
    #[Groups(['ReferenceBook', 'rbv:read'])]
    private ?int $id = null;

    #[Column(name: 'rb_type', type: 'integer')]
    #[Groups(['ReferenceBook', 'rbv'])]
    private $type = 1;

    /** @deprecated  */
    #[Column(name: 'rb_system_type', type: 'integer', nullable: true)]
    #[Groups(['ReferenceBook'])]
    private $systemType;

    #[Column(name: 'is_system', nullable: true)]
    #[Groups(['ReferenceBook', 'rbv:read'])]
    private ?bool $system ;
    /**
     * @var string
     */
    #[Column(name: 'rb_name', type: 'string')]
    #[Groups(['ReferenceBook', 'rbv'])]
    private string $name;
    /**
     * @var string|null
     */
    #[Column(name: 'rb_measure', type: 'string', nullable: true)]
    #[Groups(['ReferenceBook', 'rbv'])]
    private ?string $measure = null;

    /** @deprecated */
    #[Column(name: 'rb_user_id', type: 'integer', nullable: true)]
    private $userId;

    /**
     * @var Collection<int, ReferenceBookValue>
     */
    #[OneToMany(mappedBy: 'referenceBook', targetEntity: ReferenceBookValue::class)]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['ReferenceBook:read'])]
    private Collection $referenceBookValues;

    #[ManyToOne(targetEntity: Company::class)]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['ReferenceBook', 'rbv'])]
    private ?Company $company = null;

    /**
     * @param string $name
     * @param Company|null $company
     */
    public function __construct(string $name, ?Company $company)
    {
        $this->name = $name;
        $this->company = $company;
    }


    public function getId(): int
    {
        return (int) $this->id;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function isDateType(): bool
    {
        return $this->type === self::TYPE_RB_DATE;
    }

    /** @deprecated  */
    public function getSystemType(): int
    {
        return $this->systemType;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMeasure(): ?string
    {
        return $this->measure;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @return Collection<int, ReferenceBookValue>
     */
    public function getReferenceBookValues(): Collection
    {
        return $this->referenceBookValues;
    }

    /**
     * @param int $type
     * @return ReferenceBook
     */
    public function setType(int $type): ReferenceBook
    {
        $this->type = $type;
        return $this;
    }

    /** @deprecated  */
    public function setSystemType(int $systemType): ReferenceBook
    {
        $this->systemType = $systemType;
        return $this;
    }

    /**
     * @param string $name
     * @return ReferenceBook
     */
    public function setName(string $name): ReferenceBook
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string|null $measure
     * @return ReferenceBook
     */
    public function setMeasure(?string $measure): ReferenceBook
    {
        $this->measure = $measure;
        return $this;
    }

    /**
     * @deprecated
     * @param int $userId
     * @return ReferenceBook
     */
    public function setUserId(int $userId): ReferenceBook
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @param Collection|ReferenceBookValue[] $referenceBookValues
     * @return ReferenceBook
     */
    public function setReferenceBookValues(Collection|array $referenceBookValues): ReferenceBook
    {
        $this->referenceBookValues = $this->createCollection($referenceBookValues);
        return $this;
    }

    public function addReferenceBookValues(ReferenceBookValue $referenceBookValue): ReferenceBook
    {
        return $this->addItem($this->referenceBookValues, $referenceBookValue);
    }

    public function removeReferenceBookValues(ReferenceBookValue $referenceBookValue): ReferenceBook
    {
        $this->referenceBookValues->removeElement($referenceBookValue);
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isSystem(): ?bool
    {
        return $this->system;
    }

    /**
     * @param bool|null $system
     * @return ReferenceBook
     */
    public function setSystem(?bool $system): ReferenceBook
    {
        $this->system = $system;
        return $this;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }
}
