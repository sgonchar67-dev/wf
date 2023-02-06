<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class RequestHelper
{
    public static function getContent(Request $request)
    {
        $content = $request->getContent();
        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    public static function get(Request $request)
    {
        $content = $request->getContent();
        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }
}