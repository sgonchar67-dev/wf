<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Product\Product;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;


/**
 * MigrationVersions
 */
#[Table(name: 'migration_versions')]
#[Entity]
class MigrationVersions
{
    #[Column(name: 'version', type: 'string', length: 255, nullable: false)]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private string $version;
    /**
     * @var DateTime|DateTimeImmutable|null
     */
    /**
     * @var DateTime|DateTimeImmutable|null
     */
    #[Column(name: 'executed_at', type: 'datetime', nullable: true)]
    private ?DateTimeInterface $executedAt = null;
    #[Column(name: 'execution_time', type: 'integer', nullable: true)]
    private ?int $executionTime = null;
}
