<?php
namespace App\DataTransformer\User;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\User\UserPermissionTemplateInputDto;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermission;
use App\Domain\Entity\User\UserPermissionTemplate;
use App\Service\User\UserPermissionService;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;

final class UserPermissionTemplateEditDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private UserPermissionService $userPermissionService,
        private Security $security
    ) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function transform($object, string $to, array $context = []): object
    {
        $origin = $context['object_to_populate'];
        /** @var UserPermissionTemplateInputDto $dto */
        $dto = $object;

        if (!$this->security->isGranted(User::ROLE_OWNER)
            && !($this->security->isGranted(User::ROLE_USER) && $this->security->isGranted(UserPermission::EMPLOYERS, $origin))) {
            throw new AccessDeniedHttpException('У вас нет доступа к данной операции!');
        }
        $editUserPermissionTemplate = $this->userPermissionService->updateUserPermissionTemplate($dto, $origin);

        return $editUserPermissionTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return (
            ($context['operation_type'] === 'item') &&
            (in_array($context['item_operation_name'], ['put', 'patch'])) &&
            ($context['input']['class'] === UserPermissionTemplateInputDto::class) &&
            ($to === UserPermissionTemplate::class)
        );
    }
}