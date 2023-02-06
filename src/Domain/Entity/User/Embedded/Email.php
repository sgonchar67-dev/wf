<?php

namespace App\Domain\Entity\User\Embedded;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use Symfony\Component\Serializer\Annotation\Groups;


#[Embeddable]
#[ApiResource(
    denormalizationContext: ['groups' => ['User']],
    normalizationContext: ['groups' => ['User', 'User:read']],
)]
class Email
{
    #[Column]
    #[ApiProperty(identifier: true, example: '79996665544')]
    #[Groups(['User'])]
    private ?string $address = null;

    #[Column(options: ['default' => false])]
    #[Groups(['User:read'])]
    private bool $confirmed = false;

    /**
     * @param string|null $address
     */
    public function __construct(?string $address)
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     * @return Email
     */
    public function setAddress(?string $address): Email
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    /**
     * @param bool $confirmed
     * @return Email
     */
    public function setConfirmed(bool $confirmed): Email
    {
        $this->confirmed = $confirmed;
        return $this;
    }
}