<?php

namespace App\Security\Voter\Order;

use App\Domain\Entity\Order\Order;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermissionConstants;
use App\Domain\Entity\User\UserRolesConstants;
use App\Service\Order\OrderStateMachine\OrderActions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class OrderVoter extends Voter
{
    public function __construct(
        private OrderSecurity $orderSecurity,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        $attributes = array_merge(
            OrderActions::ACTORS, OrderActions::SUPPLIER_ACTIONS, OrderActions::CUSTOMER_ACTIONS,
        );
        return in_array($attribute, $attributes)
            && $subject instanceof Order;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var Order $order */
        $order = $subject;

        return match ($attribute) {
            OrderActions::ORDER_DELETE,
            OrderActions::ORDER_MARK_AS_SEEN,
            OrderActions::ORDER_CONFIRM,
            OrderActions::ORDER_COMPLETE => $this->orderSecurity->isGrantedToActionAsSupplier($order, $attribute),
            OrderActions::ORDER_CHECKOUT => $this->orderSecurity->isGrantedToActionAsCustomer($order, $attribute),
            default => $this->orderSecurity->isGrantedToAction($order, $attribute),
        };
    }
}
