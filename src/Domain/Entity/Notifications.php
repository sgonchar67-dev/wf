<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Product\Product;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;


/**
 * Notifications
 */
#[Table(name: 'notifications')]
#[Entity]
class Notifications
{
    #[Column(name: 'n_id', type: 'integer', nullable: false, options: ['unsigned' => true, 'comment' => 'ID записи'])]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private int $nId;
    #[Column(name: 'user_id_for', type: 'integer', nullable: false, options: ['unsigned' => true, 'comment' => 'ID пользователя, ссылка на поле user_id таблицы users (юзер, у которого в уведомлениях появится это событие)'])]
    private int $userIdFor;
    #[Column(name: 'user_id_from', type: 'integer', nullable: false, options: ['unsigned' => true, 'comment' => 'ID пользователя, ссылка на поле user_id таблицы users (юзер, который сделал действие, и уведомление по которому появится у пользователей, которые у него в сотрудничестве)'])]
    private int $userIdFrom;
    #[Column(name: 'type', type: 'boolean', nullable: false, options: ['comment' => 'Тип уведомления (возможные значения описаны в соответствующем классе)'])]
    private bool $type;
    #[Column(name: 'dt', type: 'integer', nullable: false, options: ['unsigned' => true, 'comment' => 'Дата события в формате TIMESTAMP'])]
    private int $dt;
    #[Column(name: 'viewed', type: 'boolean', nullable: false, options: ['comment' => 'Прочитано ли уведомление (1-прочитано, 0-не прочитано)'])]
    private bool $viewed;
    #[Column(name: 'additional_data', type: 'text', length: 65535, nullable: false, options: ['comment' => 'Дополнительные данные (сериализованный массив)'])]
    private string $additionalData;
}
