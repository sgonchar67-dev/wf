<?php

namespace App\Domain\Entity\ReferenceBook;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use ApiPlatform\Core\Annotation\ApiResource;


use App\Domain\Entity\Company\Company;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
    denormalizationContext: ['groups' => ['ReferenceBookValue', 'ReferenceBookValue:write']],
    normalizationContext: ['groups' => [
        'ReferenceBookValue',
        'ReferenceBookValue:read',
        'ReferenceBookValue:ReferenceBook',
        'ReferenceBook'
    ]],
)]
#[Entity]
class ReferenceBookValue
{
    public const ID_PC = 19;
    public const ID_GRAM = 14;
    public const ID_LITRE = 9;

    #[Id]
    #[Column(options: ['unsigned' => true])]
    #[GeneratedValue]
    #[Groups(['ReferenceBookValue', 'rbv:read'])]
    private ?int $id = null;

    #[ManyToOne(targetEntity: ReferenceBook::class, cascade: ['persist'], inversedBy: 'referenceBookValues')]
    #[JoinColumn(name: 'rb_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['ReferenceBookValue', 'ReferenceBookValue:ReferenceBook', 'rbv'])]
    private ReferenceBook $referenceBook;
    /**
     * @var string
     */
    #[Column(name: 'rb_value', type: 'string')]
    #[Groups(['ReferenceBookValue', 'rbv'])]
    private string $value;

    #[ManyToOne(targetEntity: Company::class)]
    #[JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['ReferenceBookValue', 'rbv'])]
    private ?Company $company = null;

    /**
     * @param ReferenceBook $referenceBook
     * @param string $value
     * @param Company|null $company
     */
    public function __construct(ReferenceBook $referenceBook, string $value, ?Company $company = null)
    {
        $this->referenceBook = $referenceBook;
        $this->value = $value;
        $this->company = $company;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReferenceBook(): ReferenceBook
    {
        return $this->referenceBook;
    }

    public function setReferenceBook(ReferenceBook $referenceBook): self
    {
        $this->referenceBook = $referenceBook;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }
}
