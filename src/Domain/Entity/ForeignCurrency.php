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
 * ForeignCurrency
 */
#[Table(name: 'foreign_currency', uniqueConstraints: ['(name="unique_index", columns={"char_code", "self_char_code"})'])]
#[Entity]
class ForeignCurrency
{
    #[Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true, 'comment' => 'Идентификатор'])]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;
    #[Column(name: 'country_id', type: 'integer', nullable: false, options: ['unsigned' => true, 'comment' => 'Идентификатор страны'])]
    private int $countryId;
    #[Column(name: 'valute_id', type: 'string', length: 255, nullable: false, options: ['comment' => 'Идентификатор валюты'])]
    private string $valuteId;
    #[Column(name: 'self_char_code', type: 'string', length: 3, nullable: false, options: ['comment' => 'Свой код страны'])]
    private string $selfCharCode;
    #[Column(name: 'char_code', type: 'string', length: 3, nullable: false, options: ['comment' => 'Код валюты (char)'])]
    private string $charCode;
    #[Column(name: 'name', type: 'string', length: 255, nullable: false, options: ['comment' => 'Название валюты'])]
    private string $name;
    #[Column(name: 'value', type: 'string', length: 255, nullable: false, options: ['comment' => 'Курс (значение)'])]
    private string $value;
    #[Column(name: 'scale', type: 'integer', nullable: false, options: ['default' => '1', 'unsigned' => true, 'comment' => 'Количество единиц иностранной валюты '])]
    private int $scale = 1;
    /**
     * @var DateTime|DateTimeImmutable
     */
    /**
     * @var DateTime|DateTimeImmutable
     */
    #[Column(name: 'date_update', type: 'datetime', nullable: false)]
    private DateTimeInterface $dateUpdate;
}
