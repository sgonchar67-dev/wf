<?php

namespace App\Controller\Cart;

use App\Controller\AbstractController;
use App\Domain\Entity\Cart\Cart;
use App\Handler\Cart\UpdateCartHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UpdateCartAction extends AbstractController
{
    public function __invoke(\App\Domain\Entity\Cart\Cart $data, UpdateCartHandler $handler): \App\Domain\Entity\Cart\Cart
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}