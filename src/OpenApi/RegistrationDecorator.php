<?php

declare(strict_types=1);

namespace App\OpenApi;

use ArrayObject;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;

final class RegistrationDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {}

    /** @noinspection NullPointerExceptionInspection */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();
        $commonProperties = [
            'phone' => [
                'type' => 'string',
                'example' => '79206689565',
            ],
            'password' => [
                'type' => 'string',
                'example' => 'p12300',
            ],
            'email' => [
                'type' => 'string',
                'example' => 'user@mail.ru',
            ],
            'roles' => [
                'type' => 'array',
                'example' => ['ROLE_ADMIN'],
            ],
        ];
        $schemas->offsetSet('RegistrationDetails', new ArrayObject([
            'type' => 'object',
            'properties' => $commonProperties + [
                'profileName' => [
                    'type' => 'string',
                    'example' => 'Profile Name',
                ],
            ],
        ]));
        $schemas->offsetSet('RegisteredUser', new ArrayObject([
            'type' => 'object',
            'properties' => $commonProperties + [
                    'id' => ['type' => 'number'],
                    'password' => ['type' => 'string', 'description' => 'hashed password'],
                    'emailConfirmed' => ['type' => 'boolean'],
                    'phoneConfirmed' => ['type' => 'boolean'],
                    'createdAt' => ['type' => 'object'],
                    'profile' => ['type' => 'object'],
                    'company' => ['type' => 'object'],
                    'employee' => ['type' => 'object'],
                    'notificationSettings' => ['type' => 'object'],
                ],
        ]));


        $pathItem = new PathItem(
            ref: 'Registered User',
            post: new Operation(
                operationId: 'postRegistrationDetailsItem',
                tags: ['Auth'],
                responses: [
                    '201' => [
                        'description' => 'Register new User',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/RegisteredUser',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Register new User.',
                requestBody: new RequestBody(
                    description: 'Register new User',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/RegistrationDetails',
                            ],
                        ],
                    ]),
                ),
            ),
        );
        $openApi->getPaths()->addPath('/api2/register', $pathItem);

        return $openApi;
    }
}