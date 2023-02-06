<?php

namespace App\Helper\Fixture;

class ReferenceHelper
{
    public static function withId(string $ref, $id): string
    {
        return "{$ref}.id:{$id}";
    }

    public static function withAttribute(string $ref, $attribute): string
    {
        return "{$ref}.attribute:{$attribute}";
    }

    public static function withSerial(string $ref, $number): string
    {
        return "{$ref}.serial:{$number}";
    }
}