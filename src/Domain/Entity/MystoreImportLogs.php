<?php

namespace App\Domain\Entity;

use App\Domain\Entity\ProductImportSettings;
use Doctrine\ORM\Mapping as ORM;

/**
 * @todo rename to MoyskladImportLog
 */
#[ORM\Entity]
class MystoreImportLogs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ProductImportSettings::class)]
    private ?ProductImportSettings $importSettings = null;

    #[ORM\Column(type: 'smallint')]
    private int $sourceImport;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $startedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $finishedAt;

    #[ORM\Column(type: 'smallint')]
    private int $status;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $countRows;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $countCreated;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $countUpdated;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $countMissed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImportSettings(): ?ProductImportSettings
    {
        return $this->importSettings;
    }

    public function setImportSettings(?ProductImportSettings $importSettings): self
    {
        $this->importSettings = $importSettings;

        return $this;
    }

    public function getSourceImport(): ?int
    {
        return $this->sourceImport;
    }

    public function setSourceImport(int $sourceImport): self
    {
        $this->sourceImport = $sourceImport;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeInterface $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCountRows(): ?int
    {
        return $this->countRows;
    }

    public function setCountRows(?int $countRows): self
    {
        $this->countRows = $countRows;

        return $this;
    }

    public function getCountCreated(): ?int
    {
        return $this->countCreated;
    }

    public function setCountCreated(?int $countCreated): self
    {
        $this->countCreated = $countCreated;

        return $this;
    }

    public function getCountUpdated(): ?int
    {
        return $this->countUpdated;
    }

    public function setCountUpdated(?int $countUpdated): self
    {
        $this->countUpdated = $countUpdated;

        return $this;
    }

    public function getCountMissed(): ?int
    {
        return $this->countMissed;
    }

    public function setCountMissed(?int $countMissed): self
    {
        $this->countMissed = $countMissed;

        return $this;
    }
}
