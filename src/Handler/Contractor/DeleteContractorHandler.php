<?php

namespace App\Handler\Contractor;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Contractor\Contractor;
use App\Exception\AccessDeniedException;
use App\Repository\Contractor\ContractorRepository;
use App\Repository\Order\OrderRepository;
use App\Service\Order\OrderUpdater;

class DeleteContractorHandler
{
    public function __construct(
        private ContractorRepository $contractorRepository,
    ) {
    }

    public function handle(\App\Domain\Entity\Contractor\Contractor $contractor, Employee $employee)
    {
       $this->contractorRepository->delete($contractor);
    }
}