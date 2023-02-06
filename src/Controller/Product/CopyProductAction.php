<?php
namespace App\Controller\Product;

use App\Controller\AbstractController;
use App\Domain\Entity\Product\Product;
use App\Service\Product\ProductService;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class CopyProductAction extends AbstractController
{
    public function __invoke(int $id, ProductService $productService): Product
    {
        $product = $productService->copyProduct($id);

        if ($product === null) {
            throw new NotFoundHttpException("Entity Product {$id} not found");
        }
        return $product;
    }
}
