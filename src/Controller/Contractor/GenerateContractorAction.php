<?php

namespace App\Controller\Contractor;

use App\Controller\AbstractController;
use App\Domain\Entity\Contractor\Contractor;
use App\Exception\AccessDeniedException;
use App\Exception\NotFoundException;
use App\Handler\Contractor\GenerateContractorHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GenerateContractorAction extends AbstractController
{
    /**
     * @throws NotFoundException
     */
    public function __invoke(GenerateContractorHandler $handler): array
    {
        return $handler->handle($this->getEmployee());
    }
}