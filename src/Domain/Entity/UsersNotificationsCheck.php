<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Product\Product;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;


/**
 * UsersNotificationsCheck
 */
#[Table(name: 'users_notifications_check', indexes: ['(name="bot_type", columns={"bot_type"})', '(name="chat_id", columns={"chat_id"})'])]
#[Entity]
class UsersNotificationsCheck
{
    #[Column(name: 'id', type: 'integer', nullable: false)]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;
    #[Column(name: 'bot_type', type: 'string', length: 127, nullable: false)]
    private string $botType;
    #[Column(name: 'chat_id', type: 'integer', nullable: false)]
    private int $chatId;
    #[Column(name: 'create_date', type: 'integer', nullable: false)]
    private int $createDate;
}
