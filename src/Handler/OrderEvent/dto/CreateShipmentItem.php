<?php

namespace App\Handler\OrderEvent\dto;

use App\Helper\ApiPlatform\IriHelper;
use App\Helper\RequestHelper;
use Symfony\Component\HttpFoundation\Request;

class CreateShipmentItem
{
    public string|int $orderProduct;

    public ?int $count;

    public static function createFromRequest(Request $request): self
    {
        $data = RequestHelper::getContent($request);
        $self = new self();
        $self->orderProduct = IriHelper::parseId($data['orderProduct']);
        $self->count = $data['count'];

        return $self;
    }


}