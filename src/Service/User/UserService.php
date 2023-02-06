<?php

namespace App\Service\User;

use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermission;
use App\Domain\Entity\User\UserPermissionTemplate;
use App\Domain\ValueObject\Username;
use App\DTO\User\CreateUserDto;
use App\DTO\User\UpdateUserDto;
use App\Factory\UserFactory;
use App\Repository\User\UserRepository;
use App\Service\EmailConfirmationCodeService;
use App\Service\PhoneConfirmationCode\PhoneConfirmationCodeService;
use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private UserFactory $userFactory,
        private PhoneConfirmationCodeService $phoneConfirmationCodeService,
        private EmailConfirmationCodeService $emailConfirmationCodeService,
    ) {
    }

    public function create(CreateUserDto $dto): \App\Domain\Entity\User\User
    {

        if ($this->userRepository->findByPhone($dto->phone) ||
            $this->userRepository->findByEmail($dto->email)) {
            throw new \DomainException("User already exists", 422);
        }
        $user = $this->userFactory->create($dto);

        $this->userRepository->save($user);

        $permissionTemplate = new UserPermissionTemplate(
            $user->getCompany(),
            UserPermission::DEFAULT_PERMISSIONS_NAME,
            UserPermission::DEFAULT_PERMISSIONS
        );

        $this->userRepository->save($permissionTemplate);

        return $user;
    }

    public function update(User $user, UpdateUserDto $dto): User
    {
        if ($dto->password && $dto->newPassword) {
            if (!$this->passwordHasher->isPasswordValid($user, $dto->password)) {
                throw new InvalidPasswordException();
            }
            $user->setPlainPassword($dto->newPassword);
            $hashedNewPassword = $this->passwordHasher->hashPassword(
                $user,
                $dto->newPassword
            );
            $user->setPassword($hashedNewPassword);
        }

        if ($dto->phone && $dto->phone !== $user->getPhone()) {
            $this->phoneConfirmationCodeService->sendCode($user, $dto->phone);
        }

        if ($dto->email && $dto->email !== $user->getEmail()) {
            $user->setEmail($dto->email);
            $this->emailConfirmationCodeService->sendCode($user, $dto->email);
        }

        $this->userRepository->save($user);

        return $user;
    }

    public function get($userId): User
    {
        return $this->userRepository->get($userId);
    }

    public function checkUsernameExists(string $username): bool
    {
        $username = new Username($username);
        $user = $username->isEmail()
            ? $this->userRepository->findByEmail($username->getValue())
            : $this->userRepository->findByPhone($username->getValue())
        ;
        return (bool) $user;
    }

    public function checkConfirmedUsernameExists(string $username): ?bool
    {
        $username = new Username($username);
        return $username->isEmail()
            ? $this->userRepository->findByEmail($username->getValue())?->isEmailConfirmed()
            : $this->userRepository->findByPhone($username->getValue())?->isPhoneConfirmed()
        ;
    }
}