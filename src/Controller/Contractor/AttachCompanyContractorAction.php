<?php

namespace App\Controller\Contractor;

use App\Controller\AbstractController;
use App\Domain\Entity\Contractor\Contractor;
use App\Handler\Contractor\UpdateContractorHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AttachCompanyContractorAction extends AbstractController
{
    public function __invoke(\App\Domain\Entity\Contractor\Contractor $data, UpdateContractorHandler $handler): \App\Domain\Entity\Contractor\Contractor
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}