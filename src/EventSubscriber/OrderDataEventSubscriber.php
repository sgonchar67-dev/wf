<?php

namespace App\EventSubscriber;

use App\Domain\Entity\Order\OrderProduct\OrderProduct;
use App\Security\SecurityInterface;
use App\Service\Order\OrderDataLog\OrderDataLogService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class OrderDataEventSubscriber implements EventSubscriber
{
    public function __construct(
        private SecurityInterface $security,
        private OrderDataLogService $orderDataLogService,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
//            Events::postPersist,
//            Events::postUpdate,
            Events::postRemove,
        ];
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->logOrderProductsChanges($args);
    }
//
//    public function postPersist(LifecycleEventArgs $args)
//    {
//        $this->logOrderProductsChanges($args);
//    }
////
////    public function postUpdate(LifecycleEventArgs $args)
////    {
////        $this->logOrderProductsChanges($args);
////    }

    private function logOrderProductsChanges(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof OrderProduct &&
            $entity->getOrder()->getPlacedAt() &&
            $entity->getOrder()->getSupplierCompany() === $this->security->getUser()->getEmployeeCompany()
        ) {
            $this->orderDataLogService->logProductsChanges($entity->getOrder(), $this->security->getUser()->getEmployee());
        }
    }
}