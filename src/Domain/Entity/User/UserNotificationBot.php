<?php

namespace App\Domain\Entity\User;

use App\Domain\Entity\User\User;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[Table]
#[Entity]

#[ApiResource(
    collectionOperations: [
        'get',
        'post' => ['security' => "is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')"]
    ],
    itemOperations: [
        'get' => ['security' => "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getUser() == user)"],
        'put' => ['security' => "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getUser() == user)"],
        'patch' => ['security' => "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getUser() == user)"],
        'delete' => ['security' => "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getUser() == user)"],
    ],
    denormalizationContext: ['groups' => ['UserNotificationBot', 'UserNotificationBot:write']],
    normalizationContext: ['groups' => ['UserNotificationBot', 'UserNotificationBot:read']]
)]
class UserNotificationBot
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    #[Groups(['UserNotificationBot:read'])]
    private ?int $id = null;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'notificationBots')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['UserNotificationBot'])]
    private User $user;

    #[Column]
    #[Groups(['UserNotificationBot'])]
    private string $botType;

    #[Column]
    #[Groups(['UserNotificationBot'])]
    private int $botId;

    #[Column(type: 'text', nullable: true)]
    private ?string $replyData = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): UserNotificationBot
    {
        $this->id = $id;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): UserNotificationBot
    {
        $this->user = $user;
        return $this;
    }

    public function getBotType(): string
    {
        return $this->botType;
    }

    public function setBotType(string $botType): UserNotificationBot
    {
        $this->botType = $botType;
        return $this;
    }

    public function getBotId(): int
    {
        return $this->botId;
    }

    public function setBotId(int $botId): UserNotificationBot
    {
        $this->botId = $botId;
        return $this;
    }

    public function getReplyData(): ?array
    {
        return $this->replyData;
    }
    
    public function setReplyData(?array $replyData): UserNotificationBot
    {
        $this->replyData = $replyData;
        return $this;
    }
}
