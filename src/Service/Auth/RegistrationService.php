<?php

namespace App\Service\Auth;

use App\DTO\User\CreateUserDto;
use App\Domain\Entity\User\User;
use App\Service\Notification\NotificationService;
use App\Service\PhoneConfirmationCode\PhoneConfirmationCodeService;
use App\Service\User\UserService;

class RegistrationService
{
    public function __construct(
        private PhoneConfirmationCodeService $confirmationCodeService,
        private UserService                  $userService,
        private NotificationService $notificationService,
        private PasswordGenerator $passwordGenerator,
    ) {
    }

    public function register(CreateUserDto $dto): User
    {
        if (!$dto->password) {
            $dto = $dto->withGeneratedPassword($this->passwordGenerator->generate());
        }

        $user = $this->userService->create($dto);

        if ($dto->isPasswordGenerated()) {
            $this->notificationService->sendPassword($user);
        }
        $this->confirmationCodeService->sendCode($user, $user->getPhone());
        return $user;
    }
}