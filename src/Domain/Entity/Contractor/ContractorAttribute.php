<?php

namespace App\Domain\Entity\Contractor;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter as APFilter;
use App\Controller\Contractor\GetContractorAttributeAvailableTypesAction;
use App\Domain\Entity\Company\Company;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [
        'get',
        'post',
        'available_types' => [
            'pagination_enabled' => false,
            'method' => 'GET',
            'path' => '/contractor_attributes/available_types',
            'controller' => GetContractorAttributeAvailableTypesAction::class,
            'read' => false
        ]
    ],
    // itemOperations: [
    //     'get', 'put', 'patch', 'delete',
    //     'available_types' => [
    //         'method' => 'GET',
    //         'path' => '/contractor_attributes/available_types',
    //         'defaults' => ['_api_receive' => false],
    //     ]
    // ],
//    denormalizationContext: ['groups' => ['Product',  'Product:write',  'rbv']],
        normalizationContext: ['groups' => ['ContractorAttribute', 'ContractorAttribute:read']],
)]
#[ApiFilter(APFilter\NumericFilter::class, properties: ['company.id'])]
#[ORM\Entity]
class ContractorAttribute
{
    private const TYPE_INT = 'integer';
    private const TYPE_FLOAT = 'float';
    private const TYPE_STRING = 'string';
    private const TYPE_DATE = 'date';
    private const TYPE_DATE_TIME = 'datetime';

    private const AVAILABLE_TYPES = [
        self::TYPE_INT => 'Целое число',
        self::TYPE_FLOAT => 'Дробное число',
        self::TYPE_STRING => 'Строка',
        self::TYPE_DATE => 'Дата',
        self::TYPE_DATE_TIME => 'Дата/время'
    ];
    
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[Groups(['ContractorAttribute:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[Groups(['ContractorAttribute'])]
    private Company $company;

    #[ORM\Column]
    #[Groups(['ContractorAttribute'])]
    private string $name;

    #[ORM\Column(length: 32, nullable: false)]
    #[Groups(['ContractorAttribute'])]
    private string $type;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (!isset(self::AVAILABLE_TYPES[$type])) {
            throw new \InvalidArgumentException("Unknown type '$type'");
        }
        $this->type = $type;
        return $this;
    }

    public static function getAvailableTypes(): array
    {
        return self::AVAILABLE_TYPES;
    }
}
