<?php

namespace App\Helper\ApiPlatform;

class IriHelper
{
    public static function parseId(?string $resourceId, $isRequired = false): ?string
    {
        if (!$resourceId) {
            return null;
        }

        if (is_numeric($resourceId)) {
            $id = (int) $resourceId;
        } else {
            $url = explode("/", $resourceId);
            $urlData = array_reverse($url);
            $id = array_shift($urlData);
        }

        return $id;
    }
}