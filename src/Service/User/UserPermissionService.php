<?php
namespace App\Service\User;

use App\Domain\Entity\Company\Employee;
use App\DTO\User\UserPermissionTemplateInputDto;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermission;
use App\Domain\Entity\User\UserPermissionTemplate;
use App\Helper\ApiPlatform\IriHelper;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserPermissionService 
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
    ) {
    }
   
    public function createUserPermissionTemplate(
        UserPermissionTemplateInputDto $userPermissionTemplateInputDto,
        Company $company
    ): UserPermissionTemplate {
        $existTemplate = $this->entityManager->getRepository(UserPermissionTemplate::class)
            ->findOneBy(['company' => $company, 'description' => $userPermissionTemplateInputDto->getDescription()]);

        if ($existTemplate) {
            throw new BadRequestException('Шаблон с таким названием существует!');
        }

        $newTemplate = new UserPermissionTemplate(
            $company,
            $userPermissionTemplateInputDto->getDescription(),
            $userPermissionTemplateInputDto->getTemplatePermission()
        );

        $this->entityManager->persist($newTemplate);

        $this->setUsersPermissionsFromTemplate($this->getUsersByInputDto($userPermissionTemplateInputDto), $newTemplate);

        return $newTemplate;
    }

    public function updateUserPermissionTemplate(
        UserPermissionTemplateInputDto $userPermissionTemplateInputDto,
        UserPermissionTemplate         $userPermissionTemplate
    ): UserPermissionTemplate
    {
        if ($userPermissionTemplate->getDescription() === UserPermission::DEFAULT_PERMISSIONS_NAME) {
            throw new BadRequestException('Запрещено изменять шаблон "По умолчанию"!!!');
        }        
        $newDescription = $userPermissionTemplateInputDto->getDescription() ?? $userPermissionTemplate->getDescription();

        /** @var UserPermissionTemplate|null $item */
        $existTemplate = $this->entityManager->getRepository(UserPermissionTemplate::class)
            ->findOneBy(['company' => $userPermissionTemplate->getCompany(), 'description' => $newDescription]);
        if ($existTemplate && $userPermissionTemplate->getId() !== $existTemplate->getId()) {
            throw new BadRequestException('Шаблон с таким названием существует!');
        }
        $newPermissions = $userPermissionTemplateInputDto->getTemplatePermission();

        $newUsers = $this->getUsersByInputDto($userPermissionTemplateInputDto);
        $oldUsers = [];
        foreach ($this->getUsersWithAttachedTemplate($userPermissionTemplate) as $user) {
            $oldUsers[$user->getId()] = $user;
        }

        /** @var User $item */
        foreach ($newUsers as $user) {
            $user->getEmployee()->setDescription($newDescription);
            $user->getPermission()->setPermissions($newPermissions);
            $this->entityManager->persist($user);
        }

        $usersDelete = array_diff_key($oldUsers, $newUsers);
        /** @var User $item */
        foreach ($usersDelete as $user) {
            $user->getEmployee()->setDescription(UserPermission::DEFAULT_PERMISSIONS_NAME);
            $user->getPermission()->setPermissions(UserPermission::DEFAULT_PERMISSIONS);
            $this->entityManager->persist($user);
        }

        $userPermissionTemplate->setDescription($newDescription)
            ->setTemplatePermission($newPermissions);
        $this->entityManager->persist($userPermissionTemplate);
        
        return $userPermissionTemplate;
    }

    /**
     * @param UserPermissionTemplate $userPermissionTemplate
     * @return User[]
     */
    public function getUsersWithAttachedTemplate(UserPermissionTemplate $userPermissionTemplate): array
    {
        return $this->entityManager->getRepository(User::class)
            ->getUsersWithAttachedTemplate($userPermissionTemplate);
    }

    /**
     * @param UserPermissionTemplate $userPermissionTemplate
     * @return Employee[]
     */
    public function getEmployeesAttachedTemplate(UserPermissionTemplate $userPermissionTemplate): array
    {
        $users =  $this->getUsersWithAttachedTemplate($userPermissionTemplate);
        return array_map(function (User $user) {
            return $user->getEmployee();
        }, $users);
    }

    /**
     * @param User[] $users
     * @param UserPermissionTemplate $permissionTemplate
     * @return void
     */
    private function setUsersPermissionsFromTemplate(array $users, UserPermissionTemplate $permissionTemplate): void
    {
        foreach ($users as $user) {
            if ($user->getEmployeeCompany() !== $permissionTemplate->getCompany()) {
                throw new BadRequestException(sprintf('Пользователь "%s" не вашей компании!', $user->getProfile()->getName()));
            }
            $userPermission = $user->getPermission();
            $userPermission->setPermissions($permissionTemplate->getTemplatePermission());

            $employee = $user->getEmployee();
            $employee->setDescription($permissionTemplate->getDescription());
            
            $this->entityManager->persist($userPermission);
            $this->entityManager->persist($employee);
        }
    }

    /**
     * @param UserPermissionTemplateInputDto $inputDto
     * @return User[]
     */
    private function getUsersByInputDto(UserPermissionTemplateInputDto $inputDto): array
    {
        $users = [];
        foreach ($inputDto->getUsers() ?? [] as $user) {
            if (!$user instanceof User) {
                $user = $this->userRepository->find(IriHelper::parseId($user));
            }
            $users[$user->getId()] = $user;
        }

        return $users;
    }
}
