<?php

namespace App\Repository\Order;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Contractor\Contractor;
use App\Domain\Entity\Order\Order;
use App\Domain\Entity\Order\OrderStatusConstants;
use App\Repository\AbstractRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @method Order get($id)
 * @method Order|null find($id)
 * @method remove(Order $entity)
 * @method delete(Order $entity)
 * @method persist(Order $entity)
 * @method save(Order $entity)
 */
class OrderRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(Order::class);
    }

    /**
     * @param Company $company
     * @return \App\Domain\Entity\Company\Employee[]
     */
    public function getOrderManagers(Company $company): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select(['o.manager'])
            ->distinct()
            ->from('order', 'o')
            ->where('o.customerCompany = :customerCompany')
            ->setParameter('customerCompany', $company)
        ;

        return $qb->getQuery()->getArrayResult();
    }

    public function getOrderStatuses(Company $company): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select(['o.status'])
            ->distinct()
            ->from('order', 'o')
            ->where('o.customerCompany = :customerCompany')
            ->setParameter('customerCompany', $company)
        ;

        return $qb->getQuery()->getArrayResult();
    }

    public function findLastNumberedByCustomer(Company $company): ?Order
    {
        return $this->repo->findOneBy(
            ['customerCompany' => $company],
            ['shotNumber' => Criteria::DESC]
        );
    }

    public function findLastNumberedDraftBySupplier(Company $supplierCompany): ?Order
    {
        return
            $this->repo->findOneBy(
                [
                    'supplierCompany' => $supplierCompany,
                    'status' => OrderStatusConstants::STATUS_DRAFT_SUPPLIER
                ],
                ['shotNumber' => Criteria::DESC]
        );
    }

    public function findLastNumberedOrderBySupplier(Company $supplierCompany): ?Order
    {
        return $this->repo->createQueryBuilder('o')
            ->andWhere('o.status is not :draft')
            ->andWhere('o.supplierCompany = :company')
            ->setParameter('company', $supplierCompany)
            ->setParameter('draft', OrderStatusConstants::STATUS_DRAFT_SUPPLIER)
            ->orderBy('o.shotNumber', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Contractor $contractor
     * @return Order[]
     */
    public function findOrdersWithoutCustomerCompanyByContractor(Contractor $contractor): array
    {
        return $this->repo->findBy([
            'contractor' => $contractor,
            'customerCompany' => NULL,
        ]);
    }

    /**
     * @param Company $company
     * @return Order[]
     */
    public function findOrdersWithoutContractorByCustomerCompany(Company $company): array
    {
        return $this->repo->findBy([
            'contractor' => NULL,
            'customerCompany' => $company,
        ]);
    }

    public function findLastNumberedByContractor(?Contractor $contractor)
    {
        return $this->repo->findOneBy(
            ['contractor' => $contractor],
            ['shotNumber' => Criteria::DESC]
        );
    }
}