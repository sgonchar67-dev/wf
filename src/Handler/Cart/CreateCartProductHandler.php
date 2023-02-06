<?php

namespace App\Handler\Cart;

use App\Domain\Entity\Cart\CartProduct;
use App\Domain\Entity\Company\Employee;
use App\Exception\AccessDeniedException;
use App\Repository\Cart\CartRepository;

class CreateCartProductHandler
{
    public function __construct(
        private CartRepository $cartRepository,
    ) {
    }

    public function handle(\App\Domain\Entity\Cart\CartProduct $cartProduct, Employee $employee)
    {
        if ($cartProduct->getCart()->getEmployee() !== $employee) {
            throw new AccessDeniedException();
        }
        $cartProduct->getCart()->addCartProduct($cartProduct);
        $this->cartRepository->save($cartProduct);
    }
}