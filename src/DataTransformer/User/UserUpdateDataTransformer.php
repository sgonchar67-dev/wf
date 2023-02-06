<?php
namespace App\DataTransformer\User;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\User\UpdateUserDto;
use App\Domain\Entity\User\User;
use App\Service\User\UserService;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class UserUpdateDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private UserService $userService,
    ) {
    }

    /**
     * {@inheritdoc}
     * @var UpdateUserDto $object
     */
    public function transform($object, string $to, array $context = []): object
    {
        $origin = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;
        return $this->userService->update($origin, $object);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof User) {
            return false;
        }

        return User::class === $to && null !== ($context['input']['class'] ?? null);
    }
}