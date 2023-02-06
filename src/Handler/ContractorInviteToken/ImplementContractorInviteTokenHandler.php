<?php

namespace App\Handler\ContractorInviteToken;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Contractor\ContractorInviteToken;
use App\Repository\Order\OrderRepository;
use App\Service\Order\OrderUpdater;

class ImplementContractorInviteTokenHandler
{
    public function __construct(
        private OrderUpdater $orderUpdater,
        private OrderRepository $orderRepository,
    ) {
    }

    public function handle(ContractorInviteToken $token, Employee $employee): void
    {
        $contractor = $token->getContractor();
        if ($token->isImplemented() || $employee->getCompany() === $contractor->getCompany()) {
            return;
        }

        if (!$contractor->getContractorCompany()) {
            $contractor->setContractorCompany($employee->getCompany());
        }

        $this->orderUpdater->updateByContractor($contractor);
        $token->implement();
        $this->orderRepository->flush();
    }
}