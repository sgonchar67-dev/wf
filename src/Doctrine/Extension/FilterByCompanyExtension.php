<?php

namespace App\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Domain\Entity\Contractor\ContractorTag;
use App\Domain\Entity\Product\Attribute;
use App\Domain\Entity\Product\ProductTag;
use App\Security\SecurityInterface;
use Doctrine\ORM\QueryBuilder;

class FilterByCompanyExtension implements QueryCollectionExtensionInterface
{
    protected const RESOURCE_CLASSES = [
        ProductTag::class,
        ContractorTag::class,
        Attribute::class,
    ];

    public function __construct(protected SecurityInterface $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null, array $context = []): void
    {
        if ($this->isFilterRequired($resourceClass, $context)) {
            $this->addWhere($queryBuilder);
        }
    }

    protected function addWhere(QueryBuilder $queryBuilder)
    {
        $company = $this->security->getUser()?->getEmployeeCompany();
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere("{$rootAlias}.company = :company");
        $queryBuilder->setParameter('company', $company);
    }

    protected function isFilterRequired(string $resourceClass, array $context = []): bool
    {
        return in_array($resourceClass, self::RESOURCE_CLASSES)
            && !array_key_exists('company.id', $context['filters']);
    }
}