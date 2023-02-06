<?php

namespace App\Handler\Company;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Domain\Entity\Company\Employee;
use App\Repository\Company\CompanyRepository;

class GetCustomersHandler
{
    public function __construct(
        private CompanyRepository $companyRepository,
    ) {
    }

    /**
     * @param Employee $employee
     * @return \App\Domain\Entity\Company\Company[]
     */
    public function handle(Employee $employee): array
    {
        return $this->companyRepository->getCustomers($employee->getCompany());
    }
}