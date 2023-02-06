<?php

namespace App\Controller\Company;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\AbstractController;
use App\Handler\Company\GetCustomersHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetCustomersAction extends AbstractController
{
    public function __invoke(Request $request, GetCustomersHandler $handler): array|Paginator
    {
//        $page = (int) $request->query->get('page', 1);
//        $itemsPerPage = (int) $request->query->get('itemsPerPage', 30);
//        return $handler->handleWithPaginator($this->getEmployee(), $page, $itemsPerPage);

        return $handler->handle($this->getEmployee());
    }
}