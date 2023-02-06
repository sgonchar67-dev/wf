<?php

namespace App\Security\Voter\Cart;

use App\Domain\Entity\Cart\Cart;
use App\Domain\Entity\User\UserRolesConstants;
use App\Security\SecurityInterface;

class CartSecurity
{
    public function __construct(private SecurityInterface $security)
    {
    }

    public function isGrantedToAction(Cart $cart): bool
    {
        return $this->security->isGranted(UserRolesConstants::ROLE_ADMIN_PURCHASES)
            && $cart->getCustomerCompany() === $this->security->getUser()?->getEmployeeCompany();
    }
}