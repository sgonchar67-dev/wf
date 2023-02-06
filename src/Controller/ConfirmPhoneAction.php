<?php

namespace App\Controller;

use App\Domain\Entity\User\PhoneConfirmationCode;
use App\Service\PhoneConfirmationCode\dto\ConfirmedPhone;
use App\Service\PhoneConfirmationCode\PhoneConfirmationCodeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ConfirmPhoneAction extends AbstractController
{
    public function __construct(
        private PhoneConfirmationCodeService $phoneConfirmationCodeService,
    ) {}

    public function __invoke(Request $request): ?PhoneConfirmationCode
    {
        $dto = ConfirmedPhone::create()->handleRequest($request);

        return $this->phoneConfirmationCodeService->confirm($dto);
    }
}