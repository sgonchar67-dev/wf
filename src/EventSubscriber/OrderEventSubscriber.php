<?php

namespace App\EventSubscriber;

use App\Domain\Entity\Order\OrderEventLog\OrderEventLog;
use App\Service\Order\OrderNotificationService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class OrderEventSubscriber implements EventSubscriber
{
    public function __construct(
        private OrderNotificationService $orderNotificationService,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof OrderEventLog) {
            $this->orderNotificationService->giveNotice($entity);
        }
    }
}