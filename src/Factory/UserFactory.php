<?php

namespace App\Factory;

use App\DTO\User\CreateUserDto;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\User\Profile;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermission;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * @param CreateUserDto $dto
     * @param User $user
     * @return User
     */
    public function update(CreateUserDto $dto, User &$user): User
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $dto->password
        );

        return $user
            ->setPhone($dto->phone)
            ->setEmail($dto->email)
            ->setPlainPassword($dto->password)
            ->setPassword($hashedPassword);
    }

    public function create(CreateUserDto $dto): User
    {
        return $this->_createNew($dto);
    }


    public function _createNew(CreateUserDto $dto): User
    {
        $user = User::create(
            $dto->phone,
            $dto->password,
            $dto->profileName,
            $dto->email,
            $dto->roles,
        );

        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $dto->password
        ));

        return $user;
    }

    private function _createOld(CreateUserDto $dto): User
    {
        $user = new User(
            $dto->phone,
            $dto->email,
            $dto->roles,
        );
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $dto->password
        );
        $profileName = $dto->profileName ?: $user->getPhone();
        $profile = new Profile($user, $profileName);
        $company = new Company($user, $profile->getName());
        $employee = Employee::create($user, $company);
        $permission = (new UserPermission($user))
            ->setPermissions(UserPermission::DEFAULT_PERMISSIONS);
        $company->addEmployee($employee)
            ->setContactPerson($employee);

        return $user
            ->setPlainPassword($dto->password)
            ->setPassword($hashedPassword)
            ->setProfile($profile)
            ->setCompany($company)
            ->setEmployee($employee)
            ->setPermission($permission);
    }
}