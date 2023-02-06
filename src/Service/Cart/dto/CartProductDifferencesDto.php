<?php

namespace App\Service\Cart\dto;

use App\Domain\Entity\Cart\CartProduct;

class CartProductDifferencesDto
{
    public $cartProductId;

    public $name;

    public $article;

    public $productFromCart = [];

    public $productFromShowcase = [];

    public function __construct(CartProduct $cartProduct)
    {
        $this->cartProductId = $cartProduct->getId();
        $this->name = $cartProduct->getProduct()?->getName();
        $this->article = $cartProduct->getProduct()?->getArticle();
        $product = $cartProduct->getProduct();
        $this->productFromCart['id'] = $product->getId();
        $this->productFromShowcase['id'] = $product->getId();
        if ($product->isActive()) {
            if ($cartProduct->getPrice() != $product->getPrice()) {
                $this->productFromCart['price'] = $cartProduct->getPrice();
                $this->productFromShowcase['price'] = $product->getPrice();
            }
            if (!$product->getPackages()->contains($cartProduct->getProductPackage())) {
                $this->productFromCart['package'] = $cartProduct->getProductPackage()->getPackType()->getName();
                $this->productFromShowcase['package'] = 'No contains';
                $this->productFromShowcase['isExists'] = false;
            } else {
                //todo changes in package
            }
        } else {
            $this->productFromShowcase['isExists'] = false;
        }
    }
}