<?php

namespace App\Domain\Entity;

use App\Domain\Entity\User\User;
use DateTime;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Table;

/**
 * @todo rename to ProductImportSettings
 */
#[Table(name: 'mystore_import_settings')]
#[Entity]
class ProductImportSettings
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    private ?int $id = null;

    #[OneToOne(
        targetEntity: User::class,
        cascade: ["persist"],
        orphanRemoval: true
    )]
    #[JoinColumn(onDelete: 'CASCADE')]
    private User $user;

    /** @deprecated */
    #[Column(name: 'product_mode', options: ['default' => 1, 'comment' => 'deprecated (1 - product mod)'])]
    private bool $productMode = true;

    /** @todo непонятно, какие источники бывают? */
    #[Column(name: 'source_import', type: 'smallint', options: ['unsigned' => true])]
    private int $importSource;

    #[Column(type: 'smallint', options: [
        'default' => 0,
        'comment' => 'Matched field name (0 - ID, 1 - Article, 2 - Name)'
    ])]
    private int $searchMode = 0;

    #[Column(name: 'import_mode', options: ['default' => 1, 'comment' => '1 - create and update, 2 - only update'])]
    private int $importMode = 1;

    #[Column(name: 'public_mode', options: ['default' => false, 'comment' => 'publish on showcase'])]
    private bool $publishMode = false;

    /** @deprecated todo create another entity ContractorImportSettings */
    #[Column(name: 'import_type', options: [
        'default' => false,
        'comment' => 'deprecated todo create another entity ContractorImportSettings (1 - products, 2 - contractors)'
    ])]
    private bool $importType = false;

    #[Column(options: ['default' => false])]
    private bool $eraseValues = false;

    #[Column(options: ['default' => false])]
    private bool $replacePicture = false;

    #[Column(options: ['default' => false])]
    private bool $uploadLeftovers = false;

    #[Column(type: 'text', nullable: true)]
    private ?string $filepath = null;

    #[Column(type: 'text', nullable: true)]
    private ?string $filename = null;

    #[Column]
    private DateTime $createdAt;

}
