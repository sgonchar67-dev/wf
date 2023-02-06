<?php

namespace App\Service\Cart;

use App\Domain\Entity\Cart\Cart;
use App\Domain\Entity\Cart\CartProduct;
use App\Repository\Cart\CartRepository;
use App\Repository\Product\ProductRepository;
use App\Service\Cart\dto\CreateCartProduct;
use App\Repository\Cart\CartProductRepository;
use App\Repository\Product\ProductPackageRepository;

class CartProductService
{
    public function __construct(
        private CartProductRepository       $repository,
        private CartRepository              $cartRepository,
        private ProductRepository           $productRepository,
        private ProductPackageRepository    $productPackageRepository
    ) {
    }


    public function create(CreateCartProduct $dto): \App\Domain\Entity\Cart\CartProduct
    {
        $cart = $this->cartRepository->get($dto->cartId);
        $product = $this->productRepository->find($dto->productId);
        if ($cart->getShowcase() !== $product->getShowcase()) {
            throw new \DomainException('the product does not belong to this showcase', 400);
        }
        $productPackage = null;
        if ($dto->productPackageId) {
            $productPackage = $this->productPackageRepository->find($dto->productPackageId);
        }

        if ($this->repository->findOneBy($cart, $product, $productPackage)) {
            throw new \DomainException('the product is already in this cart', 400);
        }
        $cartProduct = CartProduct::create(
            $cart,
            $product,
            $productPackage,
            $dto->quantity
        );
        $this->repository->persist($cartProduct);
        $this->repository->flush();

        return $cartProduct;
    }

    public function save(CreateCartProduct $form): \App\Domain\Entity\Cart\CartProduct
    {
        $cartProduct = $this->repository->find($form->id)
            ?->setQuantity($form->quantity)
            ->setProductPackage($this->productPackageRepository->find($form->productPackageId))
        ;

        $this->repository->persist($cartProduct);
        $this->repository->flush();

        return $cartProduct;
    }

    public function remove($id)
    {
        $cartProduct = $this->repository->find($id);
        $this->repository->remove($cartProduct);
        $this->repository->flush();
    }
}