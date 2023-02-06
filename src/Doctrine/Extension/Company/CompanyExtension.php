<?php

namespace App\Doctrine\Extension\Company;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\User\UserRolesConstants;
use App\Repository\Company\CompanyRepositoryAssistant;
use App\Security\SecurityInterface;
use Doctrine\ORM\QueryBuilder;

class CompanyExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private SecurityInterface $security,
        private CompanyRepositoryAssistant $companyRepositoryAssistant,
    ) {
    }


    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null, array $context = [])
    {
        $this->addWhere($queryBuilder, $queryNameGenerator, $resourceClass, $context);
    }

    private function addWhere(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $context)
    {
        if ($resourceClass !== Company::class) {
            return;
        }

        if ($this->security->isGranted(UserRolesConstants::ROLE_SUPER_ADMIN)) {
            return;
        }

        $company = $this->security->getUser()?->getEmployeeCompany();
        $partnerIds = $this->companyRepositoryAssistant->getPartnerCompanyIds($company);
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $valueParameter = $queryNameGenerator->generateParameterName('id');
        $queryBuilder->andWhere(sprintf('%s.id IN (:%s)', $rootAlias, $valueParameter))
            ->setParameter($valueParameter, $partnerIds);
        $queryBuilder->andWhere(sprintf('%s.id in (:partners)', $rootAlias));
        $queryBuilder->setParameter('partners', $partnerIds);
    }
}