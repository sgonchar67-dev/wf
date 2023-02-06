<?php

namespace App\Domain\Entity;


use App\Domain\Entity\ProductImportSettings;
use Doctrine\ORM\Mapping as ORM;

/**
 * @todo rename to ImportMatchingFields
 */
#[ORM\Entity]
class MystoreImportMatchingFields
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: ProductImportSettings::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ProductImportSettings $importSettings;

    #[ORM\Column(type: 'string', length: 255)]
    private string $wfField;

    #[ORM\Column(type: 'string', length: 255)]
    private string $fileField;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImportSettings(): ProductImportSettings
    {
        return $this->importSettings;
    }

    public function setImportSettings(ProductImportSettings $importSettings): self
    {
        $this->importSettings = $importSettings;

        return $this;
    }

    public function getWfField(): ?string
    {
        return $this->wfField;
    }

    public function setWfField(string $wfField): self
    {
        $this->wfField = $wfField;

        return $this;
    }

    public function getFileField(): ?string
    {
        return $this->fileField;
    }

    public function setFileField(string $fileField): self
    {
        $this->fileField = $fileField;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
