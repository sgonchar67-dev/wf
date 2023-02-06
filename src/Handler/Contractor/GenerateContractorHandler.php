<?php

namespace App\Handler\Contractor;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Contractor\Contractor;
use App\Repository\Company\CompanyRepository;
use App\Repository\Contractor\ContractorRepository;
use App\Service\Order\OrderUpdater;

class GenerateContractorHandler
{
    public function __construct(
        private OrderUpdater $orderUpdater,
        private ContractorRepository $contractorRepository,
        private CompanyRepository $companyRepository,
    ) {
    }

    /**
     * @param Employee $employee
     * @return Contractor[]
     */
    public function handle(Employee $employee): array
    {
        $company = $employee->getCompany();
        $excluded = $this->companyRepository->getContractorCompanies($company);
        $companies = array_merge(
            $this->companyRepository->getSuppliers($company, $excluded),
            $this->companyRepository->getCustomers($company, $excluded),
        );

        $contractors = $this->generateContractorsFromCompanies($company, $companies);

        $this->contractorRepository->flush();
        return $contractors;
    }

    /**
     * @param Company $company
     * @param Company[] $contractorCompanies
     * @return Contractor[]
     */
    private function generateContractorsFromCompanies(Company $company, array $contractorCompanies): array
    {
        return array_map(fn(Company $cc) => $this->generateContractorFromCompany($company, $cc), $contractorCompanies);
    }

    private function generateContractorFromCompany(Company $company, Company $contractorCompany): Contractor
    {
        $contractor = Contractor::create($company, $contractorCompany);
        $this->orderUpdater->updateByContractor($contractor);
        $this->contractorRepository->persist($contractor);
        return $contractor;
    }
}