<?php

namespace App\Controller;

use App\DTO\User\CreateUserDto;
use App\Service\Auth\RegistrationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends AbstractController
{
    public function __construct(
        private RegistrationService $registrationService,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function index(Request $request): Response
    {
        $dto = new CreateUserDto();
        $dto->handleRequest($request);
        $user = $this->registrationService->register($dto);

        return $this->json($user);
    }
}
