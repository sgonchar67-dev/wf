<?php

namespace App\Helper;

class ObjectHelper
{
    public static function getPropertyValue(object $object, string $property)
    {
        $closure = fn() => $this->$property ?? null;
        return $closure->call($object);
    }

    public static function setPropertyValue(object $object, string $property, $value): object
    {
        $closure = function() use ($property, $value) {
            $this->$property = $value;
            return $this;
        };
        return $closure->call($object);
    }

    public static function setId(object $object, $id): object
    {
        return self::setPropertyValue($object, 'id', $id);
    }

    public static function isPrivatePropertySet(object $object, string $property): bool
    {
        return self::getPropertyValue($object, $property) !== null;
    }

    public static function getObjectVars(object $object): array
    {
        $closure = fn() => get_object_vars($this);
        return $closure->call($object);
    }

    public static function getPropertyNames(object $object): array
    {
        $vars = self::getObjectVars($object);
        return array_keys($vars);
    }
}