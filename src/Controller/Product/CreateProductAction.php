<?php
namespace App\Controller\Product;

use App\Controller\AbstractController;
use App\Domain\Entity\Product\Product;
use App\Handler\Product\CreateProductHandler;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateProductAction extends AbstractController
{
    public function __invoke(\App\Domain\Entity\Product\Product $data, CreateProductHandler $handler): \App\Domain\Entity\Product\Product
    {
        $handler->handle($data, $this->getEmployee());
        return $data;
    }
}
