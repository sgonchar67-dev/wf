<?php

namespace App\EventListener;

use App\Domain\Entity\User\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        /** @var User $user */
        $user = $event->getUser();

        if (!$user instanceof UserInterface ||
            !$user->isPhoneConfirmed() ||
            $user->getEmployee()->isBlocked() ||
            $user->getEmployee()->isDeleted()
        ) {
            return;
        }

        $data['user_id'] = $user->getId();
        $data['bearer_token'] = "Bearer {$data['token']}";

        $event->setData($data);
    }
}