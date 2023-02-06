<?php

namespace App\Domain\Entity\User;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ConfirmEmailAction;
use App\Domain\Entity\User\User;
use DateTime;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Serializer\Annotation\Groups;

#[Entity]
#[ApiResource(
    collectionOperations: [
        'post_confirm' => [
            'method' => 'POST',
            'path' => '/email_confirmation_codes/confirm',
            'controller' => ConfirmEmailAction::class,
            'deserialize' => false,
        ],
    ],
    itemOperations: ['get'],
    denormalizationContext: ['groups' => ['EmailConfirmationCode', 'EmailConfirmationCode:write']],
    normalizationContext: ['groups' => ['EmailConfirmationCode', 'EmailConfirmationCode:read']],
)]
class EmailConfirmationCode
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    private ?int $id = null;

    #[ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['EmailConfirmationCode:read'])]
    private User $user;

    #[Column]
    #[Groups(['EmailConfirmationCode'])]
    private string $email;

    #[Column(type: 'integer', length: 6)]
    //todo remove EmailConfirmationCode group
    #[Groups(['EmailConfirmationCode:write', 'EmailConfirmationCode'])]
    private ?int $code;

    #[Column(nullable: true)]
    #[Groups(['EmailConfirmationCode:read'])]
    private ?int $attemptCount;
    
    #[Column]
    #[Groups(['EmailConfirmationCode:read'])]
    private DateTime $sentAt;

    #[Column]
    #[Groups(['EmailConfirmationCode:read'])]
    private bool $confirmed = false;

    public function __construct(User $user, string $email, int $code)
    {
        $this->user = $user;
        $this->code = $code;
        $this->email = $email;
        $this->sentAt = new DateTime();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): EmailConfirmationCode
    {
        $this->user = $user;
        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(?int $code): EmailConfirmationCode
    {
        $this->code = $code;
        return $this;
    }

    public function getAttemptCount(): ?bool
    {
        return $this->attemptCount;
    }

    public function setAttemptCount(?bool $attemptCount): EmailConfirmationCode
    {
        $this->attemptCount = $attemptCount;
        return $this;
    }

    public function getSentAt(): DateTime
    {
        return $this->sentAt;
    }

    public function setSentAt(DateTime $sentAt): EmailConfirmationCode
    {
        $this->sentAt = $sentAt;
        return $this;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): EmailConfirmationCode
    {
        $this->confirmed = $confirmed;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
