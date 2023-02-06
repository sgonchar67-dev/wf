<?php

namespace App\Handler\Company;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Repository\Company\CompanyRepository;

class GetSuppliersHandler
{
    public function __construct(
        private CompanyRepository $companyRepository,
    ) {
    }

    /**
     * @param Employee $employee
     * @return Company[]
     */
    public function handle(Employee $employee): array
    {
        return $this->companyRepository->getSuppliers($employee->getCompany());
    }
}