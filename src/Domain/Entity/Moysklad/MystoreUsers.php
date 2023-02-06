<?php

namespace App\Domain\Entity\Moysklad;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;


/**
 * @todo rename to MoyskladUserToken
 * MystoreUsers
 */
#[Table(name: 'mystore_users')]
#[Entity]
class MystoreUsers
{
    #[Column(name: 'user_id', type: 'integer', nullable: false, options: ['unsigned' => true, 'comment' => 'Идентификатор пользователя'])]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private int $userId;
    #[Column(name: 'token', type: 'string', length: 200, nullable: false, options: ['comment' => 'Токен для авторизации'])]
    private string $token;
    #[Column(name: 'auto_synchronization', type: 'boolean', nullable: true, options: ['comment' => 'Автоматическая синхронизация'])]
    private ?bool $autoSynchronization = null;
    #[Column(name: 'synchronization_period', type: 'smallint', nullable: true, options: ['comment' => 'Периодичность синхронизации'])]
    private ?int $synchronizationPeriod = null;
}
