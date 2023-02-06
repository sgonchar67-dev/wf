<?php
namespace App\DataTransformer\User;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\User\UserPermissionTemplateInputDto;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermission;
use App\Domain\Entity\User\UserPermissionTemplate;
use App\Security\SecurityInterface;
use App\Service\User\UserPermissionService;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;

final class UserPermissionTemplateInputDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private UserPermissionService $userPermissionService,
        private SecurityInterface $security
    ) {
        
    }

    /**
     * {@inheritdoc}
     * @param UserPermissionTemplate $object
     */
    public function transform($object, string $to, array $context = []): object
    {
        $user = $this->security->getUser();

        if ($this->security->isGranted(User::ROLE_OWNER) 
            || ($this->security->isGranted(User::ROLE_USER) && $this->security->isGranted(UserPermission::EMPLOYERS, $object))) {
            $company = $user->getEmployeeCompany();
        } else {
            throw new AccessDeniedHttpException('У вас нет доступа к данной операции!');
        }

        $newUserPermissionTemplate = $this->userPermissionService->createUserPermissionTemplate($object, $company);

        return $newUserPermissionTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return (
            ($context['operation_type'] === 'collection') &&
            ($context['collection_operation_name'] === 'post') &&
            ($context['input']['class'] === UserPermissionTemplateInputDto::class) &&
            ($to === UserPermissionTemplate::class)
        );
    }
}