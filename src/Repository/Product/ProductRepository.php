<?php

namespace App\Repository\Product;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Product\Product;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $this->entityManager->getRepository(Product::class);
    }

    public function getProductLastCode(Company $company): int
    {
        $query = $this->repo->createQueryBuilder('p')
            ->select('p.code')
            ->where('p.company = :company')
            ->setParameter('company', $company)
            ->getQuery();
        
        if (!count($result = $query->getArrayResult())) {
            return 0;
        } 
        $resultArray = array_column($result, 'code');

        $numbers = array_map(static fn($code) => (int) $code, $resultArray);

        return (max($numbers));
    }

    public function findProductByCompanyAndCode(Company $company, string $code): ?Product
    {
        return $this->repo->findOneBy(['code' => $code, 'company' => $company]);
    }
}
