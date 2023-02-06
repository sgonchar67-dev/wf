<?php

namespace App\Service;

use App\Domain\Entity\User\EmailConfirmationCode;
use App\Domain\Entity\User\User;
use App\Repository\EmailConfirmationCodeRepository;
use App\Repository\User\UserRepository;
use App\Service\Notification\NotificationService;

class EmailConfirmationCodeService
{
    public function __construct(
        private EmailConfirmationCodeRepository $emailConfirmationCodeRepository,
        private NotificationService             $notificationService,
        private UserRepository                  $userRepository,
    ) {
    }

    public function sendCode(User $user, $email) {
        $code = random_int(100000, 999999);
        $confirmationCode = new EmailConfirmationCode($user, $email, $code);
        $this->emailConfirmationCodeRepository->save($confirmationCode);
        $this->notificationService->sendEmailConfirmationCode($confirmationCode);
    }

    public function getByUser(User $user): ?EmailConfirmationCode
    {
        if ($user->isEmailConfirmed()) {
            return null;
        }

        return $this->emailConfirmationCodeRepository->getByUser($user);
    }

    public function confirm(User $user, string $value): ?EmailConfirmationCode
    {
        if (!$code = $this->getByUser($user)) {
            return null;
        }
        if ($code->getCode() === (int) $value) {
            $code->setConfirmed(true);
            $user->setEmail($code->getEmail());
        } else {
            $user->setEmailConfirmed(false);
            $count = $code->getAttemptCount();
            $count++;
            $code->setAttemptCount($count);
        }
        $this->emailConfirmationCodeRepository->persist($code);
        $this->userRepository->save($user);

        return $code;
    }
}