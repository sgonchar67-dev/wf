<?php
namespace App\Controller\ResourceCategory;

use App\Controller\AbstractController;
use App\Presenter\ResourceCategory\ResourceCategoryPresenter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetResourceCategoryTreeAction extends AbstractController
{
    public function __invoke(int $company_id, ResourceCategoryPresenter $presenter): JsonResponse
    {
        $tree = $presenter->getTree($company_id);

        return $this->json($tree);
    }
}
