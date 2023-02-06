<?php

namespace App\Repository;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Contractor\Contractor;
use App\Domain\Entity\Order\Order;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;

class CompanyRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(Company::class);
    }

    private function getSupplierIds(Company $company, array $excluded = []): array
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

    /**
     * @param \App\Domain\Entity\Company\Company $company
     * @param \App\Domain\Entity\Company\Company[] $excluded
     * @return Company[]
     */
    public function getSuppliers(Company $company, array $excluded = []): array
    {
        $companyIds = $this->getSupplierIds($company, $excluded);
        return $this->findByIds($companyIds);
    }

    public function getSuppliersPaginator(Company $company, array $excluded = [], $page = 1, $limit = 30): Paginator
    {
        $dql = $this->createQueryBuilderForGetSuppliers($company, $excluded)
            ->getQuery();

        return new Paginator($this->paginate($dql, $page, $limit)->setUseOutputWalkers(false));
    }

    //-------------------------------------------------

    private function getCustomersIds(Company $company, array $excluded = []): array
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

    /**
     * @param Company $company
     * @param \App\Domain\Entity\Company\Company[] $excluded
     * @return \App\Domain\Entity\Company\Company[]
     */
    public function getCustomers(Company $company, array $excluded = []): array
    {
        $companyIds = $this->getCustomersIds($company, $excluded);
        return $this->findByIds($companyIds);
    }

    public function getCustomersPaginator(Company $company, array $excluded = [], $page = 1, $limit = 30): Paginator
    {
        $dql = $this->createQueryBuilderForGetCustomerIds($company, $excluded)
            ->getQuery();

        return new Paginator($this->paginate($dql, $page, $limit)->setUseOutputWalkers(false));
    }

    /**
     * @param \App\Domain\Entity\Company\Company $company
     * @return Company[]
     */
    public function getContractorCompanies(Company $company): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('(c.contractorCompany) as companyId')
            ->distinct()
            ->from(Contractor::class, 'c')
            ->andWhere('c.contractorCompany is not null')
            ->andWhere('c.company = :company')
            ->setParameter('company', $company)
        ;
        $result = $qb->getQuery()->getResult();
        $companyIds = array_column($result, 'companyId');
        return $this->findByIds($companyIds);
    }
}