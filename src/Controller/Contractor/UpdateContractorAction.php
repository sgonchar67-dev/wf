<?php

namespace App\Controller\Contractor;

use App\Controller\AbstractController;
use App\Domain\Entity\Contractor\Contractor;
use App\Exception\AccessDeniedException;
use App\Exception\NotFoundException;
use App\Handler\Contractor\UpdateContractorHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UpdateContractorAction extends AbstractController
{
    /**
     * @throws NotFoundException
     */
    public function __invoke(Contractor $data, UpdateContractorHandler $handler): Contractor
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}