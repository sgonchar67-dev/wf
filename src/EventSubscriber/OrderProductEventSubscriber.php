<?php

namespace App\EventSubscriber;

use App\Domain\Entity\Order\Order;
use App\Domain\Entity\Order\OrderProduct\OrderProduct;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class OrderProductEventSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof OrderProduct) {
            $object->getOrder()->removeOrderProduct($object)->recalculate();
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof Order) {
            $object->recalculate();
        }
        if ($object instanceof OrderProduct) {
            $object->getOrder()->recalculate();
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof Order) {
            $object->recalculate();
        }
        if ($object instanceof OrderProduct) {
            $object->getOrder()->recalculate();
        }
    }
}
