<?php

namespace App\EventSubscriber;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Showcase\Showcase;
use App\Domain\Entity\User\User;
use App\Exception\PrivateSecurityException;
use App\Security\PrivateSecurity;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class PrivateSecurityEventSubscriber implements EventSubscriber
{
    public function __construct(
        private PrivateSecurity $privateSecurity,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove,
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Company ||
            $entity instanceof User ||
            $entity instanceof Showcase
        ) {
            try {
                $this->privateSecurity->check($entity);
            } catch (PrivateSecurityException) {
                $this->entityManager->rollback();
            }
        }
    }
}
