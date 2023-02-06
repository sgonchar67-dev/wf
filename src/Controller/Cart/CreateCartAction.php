<?php

namespace App\Controller\Cart;

use App\Controller\AbstractController;
use App\Domain\Entity\Cart\Cart;
use App\Handler\Cart\CreateCartHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateCartAction extends AbstractController
{
    public function __invoke(\App\Domain\Entity\Cart\Cart $data, CreateCartHandler $handler): \App\Domain\Entity\Cart\Cart
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}