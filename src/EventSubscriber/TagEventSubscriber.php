<?php

namespace App\EventSubscriber;

use App\Domain\Entity\Product\Product;
use App\Domain\Entity\Tag;
use App\Security\SecurityInterface;
use App\Service\Product\ProductCodeGenerator;
use App\Handler\Company\ActivateCompanyHandler;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class TagEventSubscriber implements EventSubscriber
{
    public function __construct(
        private SecurityInterface $security,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->setCompany($args);
    }

    protected function setCompany(LifecycleEventArgs $args)
    {
        $tag = $args->getObject();

        if ($tag instanceof Tag && !$tag->getCompany()) {
            $tag->setCompany($this->security->getUser()->getEmployeeCompany());
        }
    }
}
