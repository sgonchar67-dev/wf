<?php

namespace App\Controller;

use App\Domain\Entity\User\EmailConfirmationCode;
use App\Service\EmailConfirmationCodeService;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ConfirmEmailAction extends AbstractController
{
    public function __construct(
        private EmailConfirmationCodeService $emailConfirmationCodeService,
        private UserService $userService,
    ) {}

    public function __invoke(Request $request): ?EmailConfirmationCode
    {
        $content = $request->getContent();
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        $user = $this->getUser();
        $code = $data['code'] ?? null;
        $userId = $data['user'] ?? null;
        $user = $user ?: $this->userService->get($userId);
        return $this->emailConfirmationCodeService->confirm($user, $code);
    }
}
