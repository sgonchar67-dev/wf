<?php

namespace App\Handler\Cart;

use App\Domain\Entity\Cart\Cart;
use App\Domain\Entity\Company\Employee;
use App\Exception\AccessDeniedException;
use App\Repository\Cart\CartRepository;

class UpdateCartHandler
{
    public function __construct(
        private CartRepository $cartRepository,
    ) {
    }

    public function handle(\App\Domain\Entity\Cart\Cart $cart, Employee $employee)
    {
        if ($cart->getEmployee() !== $employee) {
            throw new AccessDeniedException();
        }

        $this->cartRepository->save($cart);
    }
}