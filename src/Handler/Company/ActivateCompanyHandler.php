<?php

namespace App\Handler\Company;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\ResourceCategory;
use App\Domain\Entity\User\User;
use App\Exception\AccessDeniedException;
use App\Repository\Company\CompanyRepository;
use App\Repository\ResourceCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class ActivateCompanyHandler
{
    public function __construct(
        private CompanyRepository $companyRepository,
    ) {
    }

    /**
     * @throws NonUniqueResultException
     * @throws AccessDeniedException
     */
    public function handle(Company $company, User $user): void
    {
        if ($company->getUser() !== $user) {
            throw new AccessDeniedException();
        }

        $company->activate();

        $this->companyRepository->persist($company);
        $this->companyRepository->flush();
    }
}