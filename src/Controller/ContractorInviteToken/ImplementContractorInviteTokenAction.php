<?php

namespace App\Controller\ContractorInviteToken;

use App\Controller\AbstractController;
use App\Domain\Entity\Contractor\ContractorInviteToken;
use App\Handler\ContractorInviteToken\ImplementContractorInviteTokenHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ImplementContractorInviteTokenAction extends AbstractController
{
    public function __invoke(ContractorInviteToken $data, ImplementContractorInviteTokenHandler $handler): ContractorInviteToken
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }

}