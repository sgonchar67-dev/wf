<?php

namespace App\Service;

use App\Doctrine\EntityManagerHelper;
use App\DTO\Employee\EmployeeCreateDto;
use App\DTO\Employee\EmployeeEditDto;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\User\Profile;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermission;
use App\Domain\Entity\User\UserPermissionTemplate;
use App\Helper\PhoneHelper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class EmployeeService 
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function createEmployee(EmployeeCreateDto $employeeCreateDto, Company $company): Employee
    {
        $employeeCreateDto->setUserPhone($phone = PhoneHelper::format($employeeCreateDto->getUserPhone()));

        $existPhone = $this->entityManager->getRepository(User::class)->findOneBy(['phone' => $phone]);

        if ($existPhone) {
            throw new BadRequestException('Пользователь с таким телефонным номером существует');
        }
        
        $newUser = new User($phone, null, [User::ROLE_USER]);
        $newProfile = (new Profile($newUser, $employeeCreateDto->getProfileName()))
            ->setSurname($employeeCreateDto->getProfileSurname())
            ->setPatronymic($employeeCreateDto->getProfilePatronymic());
        $newEmployee = Employee::create($newUser, $company)
            ->setDescription($employeeCreateDto->getEmployeeDescription());

        $this->createOrUpdatePermissionTemplate($company, $newEmployee, $employeeCreateDto->getUserPermissions());

        $newPermission = (new UserPermission($newUser))
            ->setUser($newUser)
            ->setPermissions($employeeCreateDto->getUserPermissions());
        
        $newUser
            ->setProfile($newProfile)
            ->setEmployee($newEmployee)
            ->setPermission($newPermission);

        $this->entityManager->persist($newUser);
        
        return $newEmployee;
    }

    public function updateEmployee(EmployeeEditDto $employeeCreateDto, Employee $updateEmployee): Employee
    {
        $this->createOrUpdatePermissionTemplate($updateEmployee->getCompany(), $updateEmployee, $employeeCreateDto->getUserPermissions());
        
        $profile = $updateEmployee->getUser()->getProfile();
        $profile->setName($employeeCreateDto->getProfileName())
            ->setSurname($employeeCreateDto->getProfileSurname())
            ->setPatronymic($employeeCreateDto->getProfilePatronymic());

        $updateEmployee
            ->setDescription($employeeCreateDto->getEmployeeDescription())
            ->getUser()->getPermission()->setPermissions($employeeCreateDto->getUserPermissions());

        $this->entityManager->persist($updateEmployee);
        
        return $updateEmployee;
    }

    public function deleteEmployee(Employee $deletedEmployee): ?Employee
    {
        try {
            $this->entityManager->remove($deletedEmployee);
            $this->entityManager->flush();
        } catch (Exception) {
            $this->entityManager = EntityManagerHelper::reopen($this->entityManager);
            $deletedEmployee = $this->entityManager->find(Employee::class, $deletedEmployee->getId());
            $deletedEmployee->delete();
            $this->entityManager->persist($deletedEmployee);
            $this->entityManager->flush();

            return $deletedEmployee;
        }

        return null;
    }

    private function createOrUpdatePermissionTemplate(Company $company, Employee $employee, array $permission): void
    {
        $existTemplate = $this->entityManager->getRepository(UserPermissionTemplate::class)
            ->findOneBy(['company' => $company, 'description' => $employee->getDescription()]);

        if (!$existTemplate) {
            $newTemplate = new UserPermissionTemplate($company, $employee->getDescription(), $permission);
            $this->entityManager->persist($newTemplate);
        }
    }
}
