<?php

namespace App\Controller\Auth;

use App\Controller\AbstractController;
use App\Helper\RequestHelper;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CheckUsernameExistsAction extends AbstractController
{
    public function __invoke(Request $request, UserService $userService): JsonResponse
    {
        $username = RequestHelper::getContent($request)['username'] ?? null;
        $code = $userService->checkUsernameExists($username) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND;
        return $this->json(null, $code);
    }
}