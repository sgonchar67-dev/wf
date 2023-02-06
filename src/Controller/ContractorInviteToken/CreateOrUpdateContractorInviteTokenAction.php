<?php

namespace App\Controller\ContractorInviteToken;

use App\Controller\AbstractController;
use App\Domain\Entity\Contractor\ContractorInviteToken;
use App\Handler\ContractorInviteToken\CreateOrUpdateContractorInviteTokenHandler;
use App\Handler\ContractorInviteToken\ImplementContractorInviteTokenHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateOrUpdateContractorInviteTokenAction extends AbstractController
{
    public function __invoke(ContractorInviteToken $data, CreateOrUpdateContractorInviteTokenHandler $handler): ContractorInviteToken
    {
        return $handler->handle($data, $this->getEmployee());
    }

}