<?php

namespace App\Domain\Entity\User;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Entity\User\User;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[Entity]
#[ApiResource(
    collectionOperations: [
        'get',
        //'post' => ['security' => "is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')"]
    ],
    itemOperations: [
        'get'=> ['security' => "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getUser() == user)"],
        'put' => ['security' => "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getUser() == user)"],
        'patch' => ['security' => "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getUser() == user)"],
        //'delete' => ['security' => "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getUser() == user)"],
    ],
    denormalizationContext: ['groups' => ['UserNotificationSettings']],
    normalizationContext: ['groups' => ['UserNotificationSettings']]
)]
class UserNotificationSettings
{
    #[Id]
    #[OneToOne(inversedBy: 'notificationSettings', targetEntity: User::class)]
    #[JoinColumn(onDelete: 'CASCADE')]
    private User $user;

    #[Column]
    #[Groups(['UserNotificationSettings'])]
    private bool $emailPurchases = false;

    #[Column]
    #[Groups(['UserNotificationSettings'])]
    private bool $emailSales = false;

    #[Column]
    #[Groups(['UserNotificationSettings'])]
    private bool $emailMessages = false;

    #[Column]
    #[Groups(['UserNotificationSettings'])]
    private bool $telegramPurchases = false;

    #[Column]
    #[Groups(['UserNotificationSettings'])]
    private bool $telegramSales = false;

    #[Column]
    #[Groups(['UserNotificationSettings'])]
    private bool $telegramMessages = false;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): UserNotificationSettings
    {
        $this->user = $user;
        return $this;
    }

    public function isEmailPurchases(): bool
    {
        return $this->emailPurchases;
    }

    public function setEmailPurchases(bool $emailPurchases): UserNotificationSettings
    {
        $this->emailPurchases = $emailPurchases;
        return $this;
    }

    public function isEmailSales(): bool
    {
        return $this->emailSales;
    }

    public function setEmailSales(bool $emailSales): UserNotificationSettings
    {
        $this->emailSales = $emailSales;
        return $this;
    }

    public function isEmailMessages(): bool
    {
        return $this->emailMessages;
    }

    public function setEmailMessages(bool $emailMessages): UserNotificationSettings
    {
        $this->emailMessages = $emailMessages;
        return $this;
    }

    public function isTelegramPurchases(): bool
    {
        return $this->telegramPurchases;
    }

    public function setTelegramPurchases(bool $telegramPurchases): UserNotificationSettings
    {
        $this->telegramPurchases = $telegramPurchases;
        return $this;
    }

    public function isTelegramSales(): bool
    {
        return $this->telegramSales;
    }

    public function setTelegramSales(bool $telegramSales): UserNotificationSettings
    {
        $this->telegramSales = $telegramSales;
        return $this;
    }

    public function isTelegramMessages(): bool
    {
        return $this->telegramMessages;
    }
    
    public function setTelegramMessages(bool $telegramMessages): UserNotificationSettings
    {
        $this->telegramMessages = $telegramMessages;
        return $this;
    }
}
