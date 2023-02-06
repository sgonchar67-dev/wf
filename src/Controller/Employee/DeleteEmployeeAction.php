<?php
namespace App\Controller\Employee;

use App\Domain\Entity\Company\Employee;
use App\Service\EmployeeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[AsController]
class DeleteEmployeeAction extends AbstractController
{
    public function __construct(
        private EmployeeService $employeeService
    ) {  
    }

    public function __invoke(Request $request)
    {
        /** @var Employee $deleteEmployee */
        $deleteEmployee = $request->attributes->get('data');

        $result = $this->employeeService->deleteEmployee($deleteEmployee);

        if ($result !== null) {
            $this->json($result, JsonResponse::HTTP_OK, ['Access-Control-Allow-Origin' => '*'])->send();
        }
    }
}
