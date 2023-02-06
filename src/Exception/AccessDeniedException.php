<?php

namespace App\Exception;

class AccessDeniedException extends \Exception
{
    public function __construct($message = "Access denied", $code = 403, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
