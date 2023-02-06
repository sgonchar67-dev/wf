<?php

namespace App\EventSubscriber;

use App\Domain\Entity\Showcase\Showcase;
use App\Domain\Entity\Showcase\ShowcaseCategory;
use App\Domain\Entity\User\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserEventSubscriber implements EventSubscriber
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->hashUserPassword($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->hashUserPassword($args);
    }

    private function hashUserPassword(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();

        if ($user instanceof User) {
            if ($user->getPlainPassword() !== null) {
                $password = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
                $user->setPassword($password);
            }
        }
    }
}
