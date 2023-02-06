<?php

namespace App\Helper;

class ArrayHelper
{
    public static function compare($array1, $array2): bool
    {
        $array1 = self::normalize($array1);
        $array2 = self::normalize($array2);
        return $array1 == $array2;
    }

    private static function normalize($array): array
    {
        $array = json_decode(json_encode($array), true);
        return self::sortByKeys($array);
    }

    private static function sortByKeys($array): array
    {
        ksort($array);
        foreach ($array as &$item) {
            if (is_array($item)) {
                $item = self::sortByKeys($item);
            }
        }
        return $array;
    }

}