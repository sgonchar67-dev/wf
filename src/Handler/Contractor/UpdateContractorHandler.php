<?php

namespace App\Handler\Contractor;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Contractor\Contractor;
use App\Exception\AccessDeniedException;
use App\Repository\Contractor\ContractorRepository;
use App\Repository\Order\OrderRepository;
use App\Service\Order\OrderUpdater;

class UpdateContractorHandler
{
    public function __construct(
        private OrderUpdater $orderUpdater,
        private ContractorRepository $contractorRepository,
    ) {
    }

    public function handle(Contractor $contractor, Employee $employee)
    {
        if (!$contractor->getCompany()) {
            $contractor->setCompany($employee->getCompany());
        }
        if ($contractor->getContractorCompany()) {
            $this->orderUpdater->updateByContractor($contractor);
        }
        $this->contractorRepository->save($contractor);
    }
}