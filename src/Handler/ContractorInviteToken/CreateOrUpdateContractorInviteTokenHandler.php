<?php

namespace App\Handler\ContractorInviteToken;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Contractor\ContractorInviteToken;
use App\Repository\Contractor\ContractorInviteTokenRepository;
use App\Service\Notification\NotificationService;
use DomainException;

class CreateOrUpdateContractorInviteTokenHandler
{
    public function __construct(
        private NotificationService $notificationService,
        private ContractorInviteTokenRepository $contractorInviteTokenRepository,
    ) {
    }

    public function handle(ContractorInviteToken $data, Employee $employee): ContractorInviteToken
    {
        if ($contractorCompany = $data->getContractor()->getContractorCompany()) {
            throw new DomainException("The Company {$contractorCompany->getId()} is already attached", 409);
        }
        if (!$token = $this->contractorInviteTokenRepository->findOneByContractor($data->getContractor())) {
            $token = $data;
        }

        $token->setOrder($data->getOrder());
        unset($data);
        $this->contractorInviteTokenRepository->save($token);
        $this->notificationService->sendContractorInviteToken($token);
        return $token;
    }
}