<?php

namespace App\Controller\Cart;

use App\Controller\AbstractController;
use App\Domain\Entity\Cart\CartProduct;
use App\Handler\Cart\CreateCartProductHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateCartProductAction extends AbstractController
{
    public function __invoke(CartProduct $data, CreateCartProductHandler $handler): CartProduct
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}