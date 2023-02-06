<?php

namespace App\Domain\Entity\Showcase;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Entity\Showcase\Showcase;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;


#[Entity]
#[Table(name: 'ssl_certificate')]
#[ApiResource]
class SSLCertificate
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    private ?int $id = null;

    #[OneToOne(inversedBy: 'sslCertificate', targetEntity: Showcase::class)]
    #[JoinColumn(onDelete: 'CASCADE')]
    private Showcase $showcase;
    #[Column]
    private string $certificate;
    #[Column]
    private string $key;
    #[Column]
    private string $chain;

    #[Pure]
    public static function create(Showcase $showcase, string $certificate, string $key, string $chain): SSLCertificate
    {
        $self = new self();
        $self->showcase = $showcase;
        $self->certificate = $certificate;
        $self->key = $key;
        $self->chain = $chain;
        return $self;
    }
    public function edit($certificate, $key, $chain): self
    {
        $this->certificate = $certificate;
        $this->key = $key;
        $this->chain = $chain;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getShowcase(): Showcase
    {
        return $this->showcase;
    }
    public function getCertificate(): string
    {
        return $this->certificate;
    }
    public function getKey(): string
    {
        return $this->key;
    }
    public function getChain(): string
    {
        return $this->chain;
    }
    //    public function getCertificateWithChain(): string
    //    {
    //        return $this->certificate  . PHP_EOL . $this->chain;
    //    }
}
