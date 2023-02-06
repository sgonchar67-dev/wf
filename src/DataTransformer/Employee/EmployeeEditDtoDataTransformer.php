<?php
namespace App\DataTransformer\Employee;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Employee\EmployeeEditDto;
use App\Domain\Entity\Company\Employee;
use App\Service\EmployeeService;

final class EmployeeEditDtoDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private EmployeeService $employeeService
    ) {  
    }

    /**
     * {@inheritdoc}
     */
    public function transform($object, string $to, array $context = []): object
    {
        /** @var EmployeeEditDto $dto */
        $dto = $object;
        /** @var Employee origin */
        $origin = $context['object_to_populate'];

        return $this->employeeService->updateEmployee($dto, $origin);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return (
            ($context['operation_type'] === 'item') &&
            (in_array($context['item_operation_name'], ['put', 'patch'])) &&
            ($context['input']['class'] === EmployeeEditDto::class) &&
            ($to === Employee::class)
        );
    }
}