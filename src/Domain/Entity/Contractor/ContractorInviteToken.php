<?php

namespace App\Domain\Entity\Contractor;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ContractorInviteToken\CreateOrUpdateContractorInviteTokenAction;
use App\Controller\ContractorInviteToken\ImplementContractorInviteTokenAction;
use App\Domain\Entity\Contractor\Contractor;
use App\Domain\Entity\Order\Order;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'security_post_denormalize' => "is_granted('CONTRACTOR_INVITE_TOKEN_CREATE', object)",
            'method' => 'post',
            'path' => '/contractor_invite_tokens',
            'controller' => CreateOrUpdateContractorInviteTokenAction::class,
            'denormalization_context' => ['groups' => ['ContractorInviteToken:create']],
        ],
    ],
    itemOperations: [
        'get',
        'put_implement' => [
            'security' => "is_granted('CONTRACTOR_INVITE_TOKEN_IMPLEMENT', object)",
            'method' => 'PUT',
            'path' => '/contractor_invite_tokens/{id}/implement',
            'controller' => ImplementContractorInviteTokenAction::class,
            'denormalization_context' => ['groups' => ['ContractorInviteToken:implement']],
            'denormalize' => false,
        ],
    ]
)]
#[ORM\Entity]
class ContractorInviteToken
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private ?Uuid $id = null;

    #[ORM\OneToOne(targetEntity: Contractor::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(['ContractorInviteToken:create', 'ContractorInviteToken:implement'])]
    private Contractor $contractor;

    #[ORM\OneToOne(targetEntity: Order::class, cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    #[Groups(['ContractorInviteToken:create'])]
    private ?Order $order = null;

    #[ORM\Column(nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $createdAt;

    #[ORM\Column(nullable: true)]
    private ?bool $implemented = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $implementedAt = null;

    public function __construct(Contractor $contractor, ?Order $order = null)
    {
        $this->contractor = $contractor;
        $this->email = $contractor->getEmail();
        $this->order = $order;
        $this->createdAt = new DateTime();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getContractor(): Contractor
    {
        return $this->contractor;
    }

    public function setContractor(Contractor $contractor): ContractorInviteToken
    {
        $this->contractor = $contractor;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Pure] public function getInviteLink(): string
    {
        $domain = $this->getContractor()->getCompany()->getShowcase()?->getDomain() ?: 'workface.ru';
        $orderPart = $this->order ? "&order={$this->order->getId()}" : '';
        return "https://{$domain}/invite_contractor?token={$this->id}{$orderPart}";
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function isImplemented(): bool
    {
        return (bool) $this->implemented;
    }

    public function getImplementedAt(): ?DateTimeInterface
    {
        return $this->implementedAt;
    }

    public function implement(): ContractorInviteToken
    {
        $this->implemented = true;
        $this->implementedAt = new DateTime();
        return $this;
    }

    /**
     * @param Order|null $order
     * @return ContractorInviteToken
     */
    public function setOrder(?Order $order): ContractorInviteToken
    {
        $this->order = $order;
        return $this;
    }

}