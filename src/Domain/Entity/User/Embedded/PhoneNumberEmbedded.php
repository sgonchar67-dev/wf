<?php

namespace App\Domain\Entity\User\Embedded;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Brick\PhoneNumber\PhoneNumber as PhoneNumberParser;
use Brick\PhoneNumber\PhoneNumberParseException;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Embeddable]
class PhoneNumberEmbedded
{
    #[Column(length: 32)]
    #[ApiProperty(identifier: true, example: '79996665544')]
    #[Groups(['User'])]
    private string $number;

    #[Column(options: ['default' => false])]
    #[Groups(['User:read'])]
    private bool $confirmed = false;

    /**
     * @throws PhoneNumberParseException
     */
    public function __construct(string $number)
    {
        $this->setNumber($number);
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     * @return PhoneNumberEmbedded
     * @throws PhoneNumberParseException
     */
    public function setNumber(string $number): PhoneNumberEmbedded
    {
        $brickPhoneNumber = PhoneNumberParser::parse($number);
        $this->number = $brickPhoneNumber->getNationalNumber();
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
     * @return PhoneNumberEmbedded
     */
    public function setConfirmed(bool $confirmed): PhoneNumberEmbedded
    {
        $this->confirmed = $confirmed;
        return $this;
    }
}