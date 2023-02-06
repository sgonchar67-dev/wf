<?php


namespace App\Repository\Product;

use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Entity\Product\Product;
use App\Domain\Entity\ReferenceBook\ReferenceBookValue;
use App\Exception\NotFoundException;
use App\Domain\Entity\Product\ProductPackage;

class ProductPackageRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $this->entityManager->getRepository(ProductPackage::class);
    }

    /**
     * @param $id
     * @return \App\Domain\Entity\Product\ProductPackage|object|null
     */
    public function find($id): ?ProductPackage
    {
        return $this->repo->find($id);
    }

    /**
     * @param ProductPackage|int $id
     * @return \App\Domain\Entity\Product\ProductPackage
     * @throws NotFoundException
     */
    public function get($id): ProductPackage
    {
        /** @var ProductPackage|null $productPackage */
        $productPackage = $this->repo->find($id);
        if (!$productPackage) {
            throw new NotFoundException("ProductPackage {$id} not found", 404);
        }

        return $productPackage;
    }

    /**
     * @param Product $product
     * @return \App\Domain\Entity\Product\ProductPackage[]
     */
    public function getByProduct(Product $product): array
    {
        return $this->repo->findBy(['product' => $product->getId()]);
    }
    
    public function add(ProductPackage $package)
    {
        $this->entityManager->persist($package);
    }
    
    public function remove($entity)
    {
        $this->entityManager->remove($entity);
    }

    /**
     * @param Product $product
     * @param ReferenceBookValue $rbValue
     * @return ProductPackage|object|null
     */
    public function findByProductAndRbValueId(Product $product, ReferenceBookValue $rbValue): ?ProductPackage
    {
        return $this->repo->findOneBy([
            'product' => $product->getId(),
            'rbvPackType' => $rbValue->getId(),
        ]);
    }
}