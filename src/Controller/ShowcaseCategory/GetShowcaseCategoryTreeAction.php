<?php
namespace App\Controller\ShowcaseCategory;

use App\Controller\AbstractController;
use App\Presenter\ShowcaseCategory\ShowcaseCategoryPresenter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetShowcaseCategoryTreeAction extends AbstractController
{
    public function __invoke(int $showcase_id, ShowcaseCategoryPresenter $presenter): JsonResponse
    {
        $tree = $presenter->getTree($showcase_id);

        return $this->json($tree);
    }
}
