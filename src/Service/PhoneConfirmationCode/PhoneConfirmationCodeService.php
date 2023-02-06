<?php

namespace App\Service\PhoneConfirmationCode;

use App\Domain\Entity\User\PhoneConfirmationCode;
use App\Domain\Entity\User\User;
use App\Exception\NotFoundException;
use App\Repository\PhoneConfirmationCodeRepository;
use App\Repository\User\UserRepository;
use App\Service\Notification\NotificationService;
use App\Service\PhoneConfirmationCode\dto\ConfirmedPhone;
use DomainException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class PhoneConfirmationCodeService
{
    public function __construct(
        private PhoneConfirmationCodeRepository $phoneConfirmationCodeRepository,
        private NotificationService             $notificationService,
        private UserRepository                  $userRepository,
        private JWTTokenManagerInterface        $jwtTokenManager,
    ) {
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function sendCode(User $user, $phone) {
        $code = random_int(1000, 9999);
        $confirmationCode = new PhoneConfirmationCode($user, $phone, $code);
        $this->phoneConfirmationCodeRepository->save($confirmationCode);
        $this->notificationService->sendPhoneConfirmationCode($confirmationCode);
    }

    /**
     * @throws NotFoundException
     */
    public function getByUser(User $user): ?PhoneConfirmationCode
    {
        if ($user->isPhoneConfirmed()) {
            return null;
        }
        return $this->phoneConfirmationCodeRepository->getByUser($user);
    }

    /**
     * @throws NotFoundException
     */
    public function confirm(ConfirmedPhone $dto): ?PhoneConfirmationCode
    {
        $user = $this->userRepository->get($dto->userId);
        if ($user->getPhone() !== $dto->phone) {
            throw new DomainException(
                "The phone {$dto->phone} is different from the confirmation phone of user {$user->getId()}",
                Response::HTTP_BAD_REQUEST
            );
        }
        if (!$code = $this->getByUser($user)) {
            throw NotFoundException::create(PhoneConfirmationCode::class);
        }

        $code->confirm($dto->code);

        $this->phoneConfirmationCodeRepository->persist($code);
        $this->userRepository->save($user);

        if ($code->isConfirmed()) {
            $token = $this->jwtTokenManager->create($user);
            $code->setToken($token);
        }

        return $code;
    }
}