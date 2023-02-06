<?php

declare(strict_types=1);

namespace App\OpenApi;

use ArrayObject;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;

final class RefreshTokenDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        if (!$schemas = $openApi->getComponents()->getSchemas()) {
            return $openApi;
        }

        $schemas->offsetSet('RefreshToken', new ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]));
        $schemas->offsetSet('RefreshCredentials', new ArrayObject([
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                ],
            ],
        ]));

        $pathItem = new PathItem(
            ref: 'Refresh JWT',
            post: new Operation(
                operationId: 'postRefreshToken',
                tags: ['Auth'],
                responses: [
                    '200' => [
                        'description' => 'Access Token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/AuthToken',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Refresh JWT.',
                requestBody: new RequestBody(
                    description: 'Refresh JWT',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/RefreshCredentials',
                            ],
                        ],
                    ]),
                ),
            ),
        );
        $openApi->getPaths()->addPath('/api2/token/refresh', $pathItem);

        return $openApi;
    }
}
