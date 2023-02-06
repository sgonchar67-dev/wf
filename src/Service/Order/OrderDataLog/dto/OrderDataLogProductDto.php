<?php

namespace App\Service\Order\OrderDataLog\dto;

use App\Domain\Entity\Order\OrderProduct\OrderProduct;

class OrderDataLogProductDto
{
    public string $name;
    public ?string $article = null;
    public float $price;
    public ?string $measure = null;
    public ?int $quantity = null;
    public ?string $package = null;

    public function __construct(
        string $name,
        ?string $article,
        float $price,
        ?string $measure,
        ?int $quantity,
        ?string $package
    ) {
        $this->name = $name;
        $this->price = $price;
        $this->measure = $measure;
        $this->quantity = $quantity;
        $this->package = $package;
        $this->article = $article;
    }

    public static function createFromOrderProduct(OrderProduct $orderProduct): OrderDataLogProductDto
    {
        return new self(
            $orderProduct->getName(),
            $orderProduct->getArticle(),
            $orderProduct->getPrice(),
            $orderProduct->getMeasure(),
            $orderProduct->getQuantity(),
            $orderProduct->getProductPackage()?->getName(),
        );
    }


    public function toArray(): array
    {
        return (array) $this;
    }
}