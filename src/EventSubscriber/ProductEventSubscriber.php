<?php

namespace App\EventSubscriber;

use App\Domain\Entity\Product\Product;
use App\Security\SecurityInterface;
use App\Service\Product\ProductCodeGenerator;
use App\Handler\Company\ActivateCompanyHandler;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ProductEventSubscriber implements EventSubscriber
{
    public function __construct(
        private SecurityInterface $security,
        private ProductCodeGenerator         $productCodeGenerator,
        private ActivateCompanyHandler $activateCompanyHandler,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preUpdate,
            Events::postPersist
        ];
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $product = $args->getObject();

        if ($product instanceof Product) {
            if (!$newCode = $this->getNewProductCode($args)) {
                return;
            }
            if ($this->productCodeGenerator->isCodeExists($product->getCompany(), $newCode)) {
                throw new BadRequestException("Code \"{$newCode}\" is exist!");
            }
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $product = $args->getObject();
        if ($product instanceof Product) {
            $company = $product->getCompany();
            if (!$company->hasAdminPermissionTemplate() ||
                !$company->getRootCategory() ||
                !$company->isActivated()
            ) {
                $this->activateCompanyHandler->handle($company, $this->security->getUser());
            }
        }
    }

    private function getNewProductCode(LifecycleEventArgs $args): ?string
    {
        $product = $args->getObject();
        $changeSet = $args->getObjectManager()
            ->getUnitOfWork()
            ->getEntityChangeSet($product);
        return array_key_exists('code', $changeSet)
            ? end($changeSet['code'])
            : null;
    }
}
