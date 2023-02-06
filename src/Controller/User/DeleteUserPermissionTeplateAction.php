<?php
namespace App\Controller\User;

use App\Service\User\UserPermissionService;
use App\Domain\Entity\User\UserPermissionTemplate;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class DeleteUserPermissionTeplateAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPermissionService $userPermissionService
    ) {  
    }

    public function __invoke(Request $request)
    {
        /** @var \App\Domain\Entity\User\UserPermissionTemplate $deleteTemplate */
        $deleteTemplate = $request->attributes->get('data');
        if ($deleteTemplate->getDescription() === \App\Domain\Entity\User\UserPermission::DEFAULT_PERMISSIONS_NAME) {
            throw new BadRequestHttpException('Запрещено удаление шаблона "По умолчанию"!!!');
        }

        $moveToTemplateName = $request->query->get('moveTo');
        if ($moveToTemplateName === null) {
            throw new BadRequestHttpException('Не указан query параметр "moveTo" (имя шаблона для переопределения).');
        } elseif ($moveToTemplateName === $deleteTemplate->getDescription()) {
            throw new BadRequestHttpException('Нельзя назначить удаляемый шаблон.');
        }
        /** @var UserPermissionTemplate:null $templateToMove */
        $templateToMove = $this->entityManager->getRepository(\App\Domain\Entity\User\UserPermissionTemplate::class)
            ->findOneBy(['company' => $deleteTemplate->getCompany(), 'description' => $moveToTemplateName]);
        if ($templateToMove === null) {
            throw new NotFoundHttpException(sprintf('Шаблон "%s" не найден.', $moveToTemplateName));
        }
        $attachedUsers = $this->userPermissionService->getUsersAttachedTemplate($deleteTemplate);

        /** @var \App\Domain\Entity\User\User $user */
        foreach ($attachedUsers as $user) {
            $user->getEmployee()->setDescription($moveToTemplateName);
            $user->getPermission()->setPermissions($templateToMove->getTemplatePermission());
            $this->entityManager->persist($user);
        }
        $this->entityManager->remove($deleteTemplate);
        $this->entityManager->flush();
    }
}
