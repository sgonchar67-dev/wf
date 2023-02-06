<?php

namespace App\Handler\Product;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Company\ResourceCategory;
use App\Domain\Entity\Product\Product;
use App\Repository\Product\ProductRepository;
use App\Repository\ResourceCategoryRepository;
use App\Service\Product\ProductCodeGenerator;

class CreateProductHandler
{
    public function __construct(
        private ResourceCategoryRepository   $resourceCategoryRepository,
        private ProductCodeGenerator         $productCodeGenerator,
        private ProductRepository            $productRepository,
    ) {
    }

    public function handle(Product $product, Employee $employee): void
    {

        if (!$product->getResourceCategory()) {
            if (!$root = $this->resourceCategoryRepository->findRoot($product->getCompany())) {
                $root = ResourceCategory::createRoot($product->getCompany());
            }
            $product->setResourceCategory($root);
        }

        if (!$product->getCode()) {
            $product->setCode($this->productCodeGenerator->generateNextCode($product->getCompany()));
        }

        $this->productRepository->save($product);
    }
}