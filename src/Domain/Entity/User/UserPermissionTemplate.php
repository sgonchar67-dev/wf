<?php

namespace App\Domain\Entity\User;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\User\DeleteUserPermissionTeplateAction;
use App\DTO\User\UserPermissionTemplateInputDto;
use App\DTO\User\UserPermissionTemplateOutputDto;
use App\Domain\Entity\Company\Company;
use App\Repository\User\UserPermissionTemplateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [
        'get' => ['security' => "is_granted('ROLE_USER')",
            'output' => UserPermissionTemplateOutputDto::class
        ],
        'post' => ['security' => "is_granted('ROLE_USER')",
            'input' => UserPermissionTemplateInputDto::class,
            'output' => UserPermissionTemplateOutputDto::class
        ]
    ],
    itemOperations: [
        'get' => ['security' => "((is_granted('ROLE_OWNER') or (is_granted('ROLE_USER') and is_granted('EMPLOYERS', object)))
                    and user.getEmployeeCompany() == object.getCompany())",
            'output' => UserPermissionTemplateOutputDto::class
        ],
        'put' => ['security' => "((is_granted('ROLE_OWNER') or (is_granted('ROLE_USER') and is_granted('EMPLOYERS', object)))
                    and user.getEmployeeCompany() == object.getCompany())",
            'input' => UserPermissionTemplateInputDto::class,
            'output' => UserPermissionTemplateOutputDto::class       
        ],
        'delete' => ['security' => "is_granted('ROLE_OWNER')",
            'controller' => DeleteUserPermissionTeplateAction::class,
            'swagger_context' => [
                'parameters' => [
                    [
                        'name' => 'moveTo',
                        'in' => 'query',
                        'description' => 'Имя шаблона (description)',
                        'required' => 'true',
                        'type' => 'string',
                        'schema' => ['type' => 'string'],
                        'style' => 'simple'
                    ]
                ]
            ]
        ]
    ],
    attributes: ["pagination_client_items_per_page" => true],
    denormalizationContext: ['groups' => ['UserPermissionTemplate', 'UserPermissionTemplate:write']],
    normalizationContext: ['groups' => ['UserPermissionTemplate', 'UserPermissionTemplate:read']]
)]
#[ORM\Entity(repositoryClass: UserPermissionTemplateRepository::class)]
class UserPermissionTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['UserPermissionTemplate:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'userPermissionTemplates')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['UserPermissionTemplate:read'])]
    private Company $company;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['UserPermissionTemplate'])]
    private string $description;

    #[ORM\Column(type: 'json')]
    #[Groups(['UserPermissionTemplate'])]
    private array $templatePermission = [];

    /**
     * @param Company $company
     * @param string $description
     * @param array $templatePermission
     */
    public function __construct(Company $company, string $description, array $templatePermission)
    {
        $this->company = $company;
        $this->description = $description;
        $this->templatePermission = $templatePermission;
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTemplatePermission(): array
    {
        return $this->templatePermission;
    }

    public function setTemplatePermission(array $templatePermission): self
    {
        $this->templatePermission = $templatePermission;

        return $this;
    }
}
