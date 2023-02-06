<?php

namespace App\EventSubscriber;

use App\Domain\Entity\Showcase\Showcase;
use App\Domain\Entity\Showcase\ShowcaseCategory;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ShowcaseEventSubscriber implements EventSubscriber
{
    public function __construct(
        private EntityManagerInterface $entityManager
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
        $showcase = $args->getObject();

        if ($showcase instanceof Showcase) {
            $showcaseRootCategory = ShowcaseCategory::create(ShowcaseCategory::ROOT_CATEGORY_NAME, ShowcaseCategory::ROOT_CATEGORY_DESCRIPTION, $showcase);
            $this->entityManager->persist($showcaseRootCategory);
        }
    }
}
