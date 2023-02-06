<?php

namespace App\DataTransformer\Order;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Domain\Entity\Order\Order;

class OrderActionDataTransformer implements DataTransformerInterface
{

    /**
     * @param $object
     * @param string $to
     * @param array $context
     * @return object
     */
    public function transform($object, string $to, array $context = []): object
    {
        return $object ;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return false;
    }
}