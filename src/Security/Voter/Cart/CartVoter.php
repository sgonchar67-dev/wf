<?php

namespace App\Security\Voter\Cart;

use App\Domain\Entity\Cart\Cart;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CartVoter extends Voter
{
    private const CART_ACTION = 'CART_ACTION';

    private const ATTRIBUTES = [
        self::CART_ACTION,
    ];

    public function __construct(
        private CartSecurity $cartSecurity,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, self::ATTRIBUTES)
            && $subject instanceof Cart;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var Cart $cart */
        $cart = $subject;

        return match ($attribute) {
            self::CART_ACTION => $this->cartSecurity->isGrantedToAction($cart),
        };
    }
}