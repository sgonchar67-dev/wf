<?php

namespace App\Controller\Employee;

use App\Controller\AbstractController;
use App\Domain\Entity\Company\Employee;
use App\Handler\Employee\GetOrderManagersHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetOrderManagersAction extends AbstractController
{
    public function __construct(private GetOrderManagersHandler $handler)
    {}
    /**
     * @return Employee[]
     */
    public function __invoke(): array
    {
        $company = $this->getEmployee()->getCompany();
        $managers = $this->handler->handle($company);
        return $managers;
    }
}