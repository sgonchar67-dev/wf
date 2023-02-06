<?php

namespace App\Domain\Entity\Moysklad;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;


/**
 * @todo rename to MoyskladReferenceBookValue
 * MystoreReferenceBooksValues
 */
#[Table(name: 'mystore_reference_books_values', indexes: ['(name="mystore_uom_id", columns={"mystore_uom_id"})'])]
#[Entity]
class MystoreReferenceBooksValues
{
    #[Column(name: 'user_id', type: 'integer', nullable: false, options: ['unsigned' => true, 'comment' => 'user_id из таблицы users'])]
    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    private int $userId;
    #[Column(name: 'rbv_id', type: 'integer', nullable: false, options: ['unsigned' => true, 'comment' => 'rbv_id из таблицы reference_book_value'])]
    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    private int $rbvId;
    #[Column(name: 'mystore_uom_id', type: 'string', length: 200, nullable: true, options: ['comment' => 'Идентификатор еденицы измерения в моём складе'])]
    private ?string $mystoreUomId = null;
    #[Column(name: 'mystore_uom_code', type: 'string', length: 200, nullable: true, options: ['comment' => 'Код еденицы измерения в моём складе'])]
    private ?string $mystoreUomCode = null;
}
