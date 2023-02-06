<?php

namespace App\Service\Cart\dto;

class CreateCart
{
    public $showcaseId;
    public array $products = [];

    public static function create($data): self
    {
        $self = new self();
        $self->showcaseId = $data['showcaseId'];
        foreach ($data['products'] ?? [] as $productData) {
            $self->products[] = CreateCartProduct::create($productData);
        }

        return $self;
    }
}