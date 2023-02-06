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
 * MystoreGoodsProducts
 */
#[Table(name: 'mystore_goods_products', indexes: ['(name="mystore_goods_products_products_id_fk", columns={"product_id"})'])]
#[Entity]
class MystoreGoodsProducts
{
    #[Column(name: 'mystore_product_id', type: 'string', length: 50, nullable: false)]
    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    private string $mystoreProductId = '';
    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[OneToOne(targetEntity: Product::class)]
    #[JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    private Product $product;
}
