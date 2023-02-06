<?php


namespace App\Exception;


use Exception;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class NotFoundException extends Exception
{
    #[Pure] public function __construct($message = "Entity not found", $code = Response::HTTP_NOT_FOUND, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create($objectOrClass, $params = null): static
    {
        $reflect = new ReflectionClass($objectOrClass);
        $name = $reflect->getShortName();
        $paramsInfo = $params ? PHP_EOL . 'Params: ' . json_encode($params, JSON_THROW_ON_ERROR) : '';
        return new NotFoundException("Entity {$name} not found {$paramsInfo}", Response::HTTP_NOT_FOUND);
    }
}