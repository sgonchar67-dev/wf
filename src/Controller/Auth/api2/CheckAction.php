<?php

namespace App\Controller\Auth\api2;

use App\Controller\AbstractController;
use App\Helper\RequestHelper;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class CheckAction extends AbstractController
{
    #[Route(
        path: '/api2/check',
        name: 'auth_check',
        defaults: [
            '_api_item_operation_name' => 'api2_auth_check',
        ],
        methods: ['POST'],
    )]
    public function __invoke(Request $request, UserService $userService): JsonResponse
    {
        $username = RequestHelper::getContent($request)['username'] ?? null;
        $case = $userService->checkUsernameExists($username) ? 'TO_AUTH' : 'TO_REG';
        return $this->json(['case' => $case]);
    }
}