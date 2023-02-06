<?php

namespace App\Domain\Entity\Moysklad;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;


/**
 * @todo rename to MoyskladResourceCategory
 * MystoreResourceCategories
 */
#[Table(name: 'mystore_resource_categories', indexes: ['(name="mystore_resource_category_id", columns={"mystore_resource_category_id"})'])]
#[Entity]
class MystoreResourceCategories
{
    #[Column(name: 'resource_category_id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private int $resourceCategoryId;
    #[Column(name: 'mystore_resource_category_id', type: 'string', length: 200, nullable: true, options: ['comment' => 'Идентификатор товара в моём складе'])]
    private ?string $mystoreResourceCategoryId = null;
}
