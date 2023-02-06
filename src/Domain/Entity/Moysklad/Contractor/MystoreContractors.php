<?php

namespace App\Domain\Entity\Moysklad\Contractor;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;


/**
 * @deprecated todo remove use MoyskladContractor
 * MystoreContractors
 */
#[Table(name: 'mystore_contractors', indexes: ['(name="mystore_contractor_id", columns={"mystore_contractor_id"})'])]
#[Entity]
class MystoreContractors
{
    #[Column(name: 'contractor_id', type: 'integer', nullable: false, options: ['unsigned' => true, 'comment' => 'contractor_id из таблицы contractors'])]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private int $contractorId;
    #[Column(name: 'mystore_contractor_id', type: 'string', length: 200, nullable: true, options: ['comment' => 'Идентификатор контрагента в моём складе'])]
    private ?string $mystoreContractorId = null;
}
