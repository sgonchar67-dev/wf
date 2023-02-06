<?php

namespace App\Domain\Entity\Moysklad;

use App\Domain\Entity\Product\Product;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;


/**
 * @todo rename to MoyskladProduct
 * MystoreGoods
 */
#[Table(name: 'mystore_goods', indexes: ['(name="mystore_product_id", columns={"mystore_product_id"})'])]
#[Entity]
class MystoreGoods
{
    #[Column(name: 'mystore_product_id', type: 'string', length: 200, nullable: true, options: ['comment' => 'Идентификатор товара в моём складе'])]
    private ?string $mystoreProductId = null;
    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[OneToOne(targetEntity: Product::class)]
    #[JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    private Product $product;
}
