<?php

namespace App\EventListener;

use App\Domain\Entity\User\User;
use App\Repository\User\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;

class JWTDecodedListener
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function onJWTDecoded(JWTDecodedEvent $event)
    {
        $payload = $event->getPayload();
        $user = $this->userRepository->find($payload['id']);

        if (!$user instanceof User ||
            !$user->isPhoneConfirmed() ||
            $user->getEmployee()->isBlocked() ||
            $user->getEmployee()->isDeleted()
        ) {
            $event->markAsInvalid();
        }
        $payload['id'] = (string) $user->getId();
        $event->setPayload($payload);
    }
}