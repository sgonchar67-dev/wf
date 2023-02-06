<?php

namespace App\Service\Product;

use App\Domain\Entity\Company\Company;
use App\Repository\Product\ProductRepository;

class ProductCodeGenerator
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    public function generateNextCode(Company $company): string
    {
        $counter = $this->productRepository->getProductLastCode($company);

        return \str_pad((string) ++$counter, 5, '0', STR_PAD_LEFT);
    }

    public function isCodeExists(Company $company, $code): bool
    {
        return (bool) $this->productRepository->findProductByCompanyAndCode($company, $code);
    }
}