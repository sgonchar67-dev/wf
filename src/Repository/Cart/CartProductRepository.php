<?php

namespace App\Repository\Cart;

use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Entity\Cart\Cart;
use App\Domain\Entity\Cart\CartProduct;
use App\Domain\Entity\Product\Product;
use App\Domain\Entity\Product\ProductPackage;
use App\Exception\NotFoundException;

class CartProductRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(CartProduct::class);
    }

    public function find($id): ?CartProduct
    {
        /** @var \App\Domain\Entity\Cart\CartProduct|null $cartProduct */
        $cartProduct = $this->repo->find($id);
        return $cartProduct;
    }

    /**
     * @param $id
     * @return \App\Domain\Entity\Cart\CartProduct
     * @throws NotFoundException
     */
    public function get($id): \App\Domain\Entity\Cart\CartProduct
    {
        if(!$cartProduct = $this->find($id)) {
            throw new NotFoundException("CartProduct {$id} not found");
        }

        return $cartProduct;
    }

    /**
     * @param Cart $cart
     * @param Product $product
     * @param \App\Domain\Entity\Product\ProductPackage|null $productPackage
     * @return \App\Domain\Entity\Cart\CartProduct|object|null
     */
    public function findOneBy(Cart $cart, Product $product, ?ProductPackage $productPackage): ?\App\Domain\Entity\Cart\CartProduct
    {
        return $this->repo->findOneBy([
            'cart' => $cart,
            'product' => $product,
            'productPackage' => $productPackage,
        ]);
    }
}