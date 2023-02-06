<?php

namespace App\Service\Cart\dto;

use App\Helper\ApiPlatform\IriHelper;
use App\Helper\RequestHelper;
use Symfony\Component\HttpFoundation\Request;


class CreateCartProduct
{
    public $id;
    public $productId;
    public $cartId;
    public $quantity;
    public $productPackageId;

    /** @deprecated  */
    public static function create($data): self
    {
        $self = new self();
        $self->productId = $data['productId'];
        $self->quantity = $data['quantity'];
        $self->productPackageId = $data['productPackageId'] ?? null;
        $self->cartId = $data['cartId'] ?? null;

        return $self;
    }

    public static function createFromRequest(Request $request): self
    {
        $data = RequestHelper::getContent($request);
        $self = new self();
        $self->productId = IriHelper::parseId($data['productId']);
        $self->quantity = $data['quantity'];
        $self->productPackageId = IriHelper::parseId($data['productPackageId'] ?? null);
        $self->cartId = IriHelper::parseId($data['cartId'] ?? null);

        return $self;
    }

    public function withId($id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }
}
