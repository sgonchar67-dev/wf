<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Product\Product;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;


/**
 * ForeignCurrencyList
 */
#[Table(name: 'foreign_currency_list')]
#[Entity]
class ForeignCurrencyList
{
    #[Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true, 'comment' => 'Идентификатор записи'])]
    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    private ?int $id = null;
    #[Column(name: 'country_id', type: 'integer', nullable: false, options: ['unsigned' => true, 'comment' => 'Идентификатор страны'])]
    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    private int $countryId;
    #[Column(name: 'link_cbr', type: 'string', length: 255, nullable: false, options: ['comment' => 'Ссылка на центра банк страны '])]
    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    private string $linkCbr;
    #[Column(name: 'status', type: 'boolean', nullable: false, options: ['default' => '1', 'comment' => 'Статус'])]
    private bool $status = true;
}
