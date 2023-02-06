<?php
namespace App\DataTransformer\Employee;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Employee\EmployeeCreateDto;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermission;
use App\Service\EmployeeService;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;

final class EmployeeCreateDtoDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private EmployeeService $employeeService,
        private Security $security
    ) {  
    }

    /**
     * @param EmployeeCreateDto $object
     * @param string $to
     * @param array $context
     * @return EmployeeCreateDto
     * {@inheritdoc}
     */
    public function transform($object, string $to, array $context = []): object
    {
        /**@var User $user */
        $user = $this->security->getUser();

        if ($this->security->isGranted(User::ROLE_OWNER) 
            || ($this->security->isGranted(User::ROLE_USER) && $this->security->isGranted(UserPermission::EMPLOYERS, $object))) {
            $company = $user->getEmployeeCompany();
        } else {
            throw new AccessDeniedHttpException('У вас нет доступа к данной операции!');
        }
        return $this->employeeService->createEmployee($object, $company);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return (
            ($context['operation_type'] === 'collection') &&
            ($context['collection_operation_name'] === 'post') &&
            ($context['input']['class'] === EmployeeCreateDto::class) &&
            ($to === Employee::class)
        );
    }
}