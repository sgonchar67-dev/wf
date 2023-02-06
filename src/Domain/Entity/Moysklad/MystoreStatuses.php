<?php

namespace App\Domain\Entity\Moysklad;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;


/**
 * @todo rename to MoyskladUserStatus
 * MystoreStatuses
 */
#[Table(name: 'mystore_statuses', indexes: ['(name="mystore_status_id", columns={"mystore_status_id"})'])]
#[Entity]
class MystoreStatuses
{
    #[Column(name: 'status_id', type: 'integer', nullable: false, options: ['unsigned' => true, 'comment' => 'status_id из таблицы contractors'])]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private int $statusId;
    #[Column(name: 'mystore_status_id', type: 'string', length: 200, nullable: true, options: ['comment' => 'Идентификатор статуса в моём складе'])]
    private ?string $mystoreStatusId = null;
}
