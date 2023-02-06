<?php

declare(strict_types=1);

namespace App\OpenApi\api2;

use ArrayObject;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;

final class CheckDecorator implements OpenApiFactoryInterface
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

        $schemas->offsetSet('Case', new ArrayObject([
            'type' => 'object',
            'properties' => [
                'case' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]));
        $schemas->offsetSet('Username', new ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => '79164208587',
                ],
            ],
        ]));

        $pathItem = new PathItem(
            ref: 'Case',
            post: new Operation(
                operationId: 'checkUsername',
                tags: ['Auth'],
                responses: [
                    '200' => [
                        'description' => 'Check Username',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Case',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Check Username.',
                requestBody: new RequestBody(
                    description: 'Username',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Username',
                            ],
                        ],
                    ]),
                ),
            ),
        );
        $openApi->getPaths()->addPath('/api2/check', $pathItem);

        return $openApi;
    }
}
