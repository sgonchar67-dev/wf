<?php

namespace App\Repository\Company;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Order\Order;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

class CompanyRepositoryAssistant
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function getPartnerCompanyIds(Company $company, array $excluded = []): array
    {
        return array_merge(
            $this->getSupplierIds($company, $excluded),
            $this->getCustomersIds($company, $excluded)
        );
    }

    public function getSupplierIds(Company $company, array $excluded = []): array
    {
        $qb = $this->createQueryBuilderForGetSuppliers($company, $excluded);

        $result = $qb->getQuery()->getResult();
        return array_column($result, 'companyId');
    }

    private function createQueryBuilderForGetSuppliers(Company $company, array $excluded = []): QueryBuilder
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('(o.supplierCompany) as companyId')
            ->distinct()
            ->from(Order::class, 'o')
            ->leftJoin('o.supplierCompany', 'sc', Expr\Join::WITH, 'o.supplierCompany is not null')
            ->where('o.customerCompany = :company')
            ->setParameter('company', $company)
        ;
        if ($excluded) {
            $excludedContractorCompanies = implode(',', array_map(fn(Company $c) => $c->getId(), $excluded));
            $qb->andWhere("o.supplierCompany not in ({$excludedContractorCompanies})");
        }

        return $qb;
    }

    public function getCustomersIds(Company $company, array $excluded = []): array
    {
        $qb = $this->createQueryBuilderForGetCustomerIds($company, $excluded);

        $result = $qb->getQuery()->getResult();
        return array_column($result, 'companyId');
    }

    private function createQueryBuilderForGetCustomerIds(Company $company, array $excluded = []): QueryBuilder
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('(o.customerCompany) as companyId')
            ->distinct()
            ->from(Order::class, 'o')
            ->leftJoin('o.customerCompany', 'sc', Expr\Join::WITH, 'o.customerCompany is not null')
            ->where('o.supplierCompany = :company')
            ->setParameter('company', $company)
        ;
        if ($excluded) {
            $excludedContractorCompanies = implode(',', array_map(fn(Company $c) => $c->getId(), $excluded));
            $qb->andWhere("o.customerCompany not in ({$excludedContractorCompanies})");
        }

        return $qb;
    }

}