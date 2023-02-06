<?php

namespace App\Handler\Cart;

use App\Domain\Entity\Cart\Cart;
use App\Domain\Entity\Company\Employee;
use App\Repository\Cart\CartRepository;

class CreateCartHandler
{
    public function __construct(
        private CartRepository $cartRepository,
    ) {
    }

    public function handle(Cart $cart, Employee $employee)
    {
        foreach ($cart->getCartProducts() as $item) {
            $item->setCart($cart);
        }
        $cart->setEmployee($employee);
        $this->cartRepository->save($cart);
    }
}