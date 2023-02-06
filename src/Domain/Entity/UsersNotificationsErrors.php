<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Product\Product;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;


/**
 * UsersNotificationsErrors
 */
#[Table(name: 'users_notifications_errors')]
#[Entity]
class UsersNotificationsErrors
{
    #[Column(name: 'id', type: 'integer', nullable: false)]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;
    #[Column(name: 'title', type: 'string', length: 511, nullable: false)]
    private string $title;
    #[Column(name: 'error_date', type: 'integer', nullable: false)]
    private int $errorDate;
}
