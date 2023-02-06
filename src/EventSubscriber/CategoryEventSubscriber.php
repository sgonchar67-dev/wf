<?php

namespace App\EventSubscriber;

use App\Domain\Entity\Company\ResourceCategory;
use App\Domain\Entity\Showcase\ShowcaseCategory;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CategoryEventSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove,
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $category = $args->getObject();

        if ($category instanceof ShowcaseCategory ||
            $category instanceof ResourceCategory
        ) {
            $this->moveProductsToParentNode($category);
        }
    }

    private function moveProductsToParentNode(ResourceCategory|ShowcaseCategory $category): void
    {
        $category->getParent()?->setProducts($category->getProducts());
    }
}
