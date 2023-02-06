<?php

namespace App\EventSubscriber;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\User\UserRolesConstants;
use App\Exception\AccessDeniedException;
use App\Handler\Company\ActivateCompanyHandler;
use App\Security\SecurityInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CompanyEventSubscriber implements EventSubscriber
{
    public function __construct(
        private SecurityInterface $security,
        private ActivateCompanyHandler $activateCompanyHandler,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate,
        ];
    }

    /**
     * @throws NonUniqueResultException
     * @throws AccessDeniedException
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $company = $args->getObject();

        if ($company instanceof Company) {
            $this->checkoutCompany($company);
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws AccessDeniedException
     */
    private function checkoutCompany(Company $company): void
    {
        if (!$this->security->isGranted(UserRolesConstants::ROLE_OWNER)) {
            return;
        }
        if (!$company->hasAdminPermissionTemplate() ||
            !$company->getRootCategory() ||
            !$company->isActivated()
        ) {
            $this->activateCompanyHandler->handle($company, $this->security->getUser());
        }
    }
}
