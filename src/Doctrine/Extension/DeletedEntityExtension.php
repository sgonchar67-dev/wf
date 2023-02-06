<?php

namespace App\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\User\User;
use Doctrine\ORM\QueryBuilder;

class DeletedEntityExtension implements QueryCollectionExtensionInterface
{
    private const RESOURCE_CLASSES = [
        User::class,
        Employee::class,
        Company::class,
    ];

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null, array $context = [])
    {
        $this->addWhere($queryBuilder, $resourceClass, $context);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass, array $context = [])
    {
        if (!in_array($resourceClass, self::RESOURCE_CLASSES)) {
            return;
        }

        if ($this->isAppliedFilter($context)) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.deleted != :deleted', $rootAlias));
        $queryBuilder->setParameter('deleted', true);
    }

    private function isAppliedFilter($context): bool
    {
        return isset($context['filters']) && array_key_exists('deleted', $context['filters']);
    }
}