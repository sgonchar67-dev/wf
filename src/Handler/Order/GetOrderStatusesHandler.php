<?php

namespace App\Handler\Order;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Repository\EmployeeRepository;
use App\Repository\Order\OrderRepository;

class GetOrderStatusesHandler
{
    public function __construct(private OrderRepository $orderRepository)
    {
    }

    public function handle(Employee $employee): array
    {
        return $this->orderRepository->getOrderStatuses($employee->getCompany());
    }
}