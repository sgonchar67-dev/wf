<?php

namespace App\Controller\Company;

use App\Controller\AbstractController;
use App\Domain\Entity\Company\Company;
use App\Exception\AccessDeniedException;
use App\Exception\NotFoundException;
use App\Handler\Company\ActivateCompanyHandler;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ActivateCompanyAction extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws AccessDeniedException
     */
    public function __invoke(Company $data, ActivateCompanyHandler $handler): Company
    {
        $handler->handle($data, $this->getUser());
        return $data;
    }
}