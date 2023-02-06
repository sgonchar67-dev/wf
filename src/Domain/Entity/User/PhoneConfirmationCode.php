<?php

namespace App\Domain\Entity\User;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ConfirmPhoneAction;
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
            'path' => '/phone_confirmation_codes/confirm',
            'controller' => ConfirmPhoneAction::class,
            'deserialize' => false,
        ],
    ],
    itemOperations: ['get'],
    denormalizationContext: ['groups' => ['PhoneConfirmationCode', 'PhoneConfirmationCode:write']],
    normalizationContext: ['groups' => ['PhoneConfirmationCode', 'PhoneConfirmationCode:read']],
)]
class PhoneConfirmationCode
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    private ?int $id = null;

    #[ManyToOne(targetEntity: User::class)] //, inversedBy: 'confirmationCodes'
    #[JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['PhoneConfirmationCode'])]
    private User $user;

    #[Column(length: 32)]
    #[Groups(['PhoneConfirmationCode'])]
    private string $phone;

    #[Column(type: 'smallint', length: 6)]
    //todo remove PhoneConfirmationCode group
    #[Groups(['PhoneConfirmationCode:write', 'PhoneConfirmationCode'])]
    private ?int $code;

    #[Column(nullable: true)]
    #[Groups(['PhoneConfirmationCode:read'])]
    private ?int $attemptCount;
    #[Column]
    #[Groups(['PhoneConfirmationCode:read'])]
    private DateTime $sentAt;

    #[Column]
    #[Groups(['PhoneConfirmationCode:read'])]
    private bool $confirmed = false;
    #[Groups(['PhoneConfirmationCode:read'])]
    private ?string $token = null;

    public function __construct(User $user, string $phone, int $code)
    {
        $this->user = $user;
        $this->code = $code;
        $this->phone = $phone;
        $this->sentAt = new DateTime();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): PhoneConfirmationCode
    {
        $this->user = $user;
        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(?int $code): PhoneConfirmationCode
    {
        $this->code = $code;
        return $this;
    }

    public function getAttemptCount(): ?bool
    {
        return $this->attemptCount;
    }

    public function setAttemptCount(?bool $attemptCount): PhoneConfirmationCode
    {
        $this->attemptCount = $attemptCount;
        return $this;
    }

    public function getSentAt(): DateTime
    {
        return $this->sentAt;
    }

    public function setSentAt(DateTime $sentAt): PhoneConfirmationCode
    {
        $this->sentAt = $sentAt;
        return $this;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): PhoneConfirmationCode
    {
        $this->confirmed = $confirmed;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return PhoneConfirmationCode
     */
    public function setToken(?string $token): PhoneConfirmationCode
    {
        $this->token = $token;
        return $this;
    }

    public function confirm(string|int $value): self
    {
        if ($this->code === (int) $value) {
            $this->confirmed = true;
            $this->user->setPhone($this->getPhone());
            $this->user->confirmPhone();
        } else {
            $this->attemptCount++;
        }

        return $this;
    }
}
