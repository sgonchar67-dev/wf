<?php
namespace App\Controller\Product;

use App\Domain\Entity\Product\ProductPackage;
use App\Service\Product\ProductService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[AsController]
class DeleteProductPackageAction extends AbstractController
{
    public function __construct(
        private ProductService $productService
    ) {  
    }

    public function __invoke(Request $request)
    {
        /** @var \App\Domain\Entity\Product\ProductPackage $deletePackage */
        $deletePackage = $request->attributes->get('data');

        $result = $this->productService->deleteProductPackage($deletePackage);

        if ($result !== null) {
            $this->json($result, JsonResponse::HTTP_OK, ['Access-Control-Allow-Origin' => '*'])->send();
        }
    }
}
