<?php

namespace App\Doctrine\Extension\Order;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Domain\Entity\Order\Order;
use App\Security\SecurityInterface;
use Doctrine\ORM\QueryBuilder;

class OrderFilterByCompanyExtension implements QueryCollectionExtensionInterface
{
    public function __construct(private SecurityInterface $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null, array $context = [])
    {
        $this->addWhere($queryBuilder, $resourceClass, $context);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass, array $context = [])
    {
        if (Order::class !== $resourceClass) {
            return;
        }

        if (array_key_exists('customerCompany.id', $context['filters']) ||
            array_key_exists('supplierCompany.id', $context['filters'])
        ) {
            return;
        }
        $company = $this->security->getUser()?->getEmployeeCompany();
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere("{$rootAlias}.customerCompany = :company or {$rootAlias}.supplierCompany = :company");
        $queryBuilder->setParameter('company', $company);
    }
}