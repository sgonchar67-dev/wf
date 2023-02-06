<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Product\Product;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;


/**
 * UsersUid
 * @deprecated
 */
#[Table(name: 'users_uid')]
#[Entity]
class UsersUid
{
    #[Column(name: 'user_id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private int $userId;
    #[Column(name: 'u_id', type: 'string', length: 330, nullable: false)]
    private string $uId;
}
