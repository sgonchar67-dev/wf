<?php

namespace App\Handler\Employee;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Repository\EmployeeRepository;
use App\Repository\Order\OrderRepository;

class GetOrderManagersHandler
{
    public function __construct(private OrderRepository $orderRepository)
    {
    }

    /**
     * @param Company $company
     * @return \App\Domain\Entity\Company\Employee[]
     */
    public function handle(Company $company): array
    {
        return $this->orderRepository->getOrderManagers($company);
    }
}