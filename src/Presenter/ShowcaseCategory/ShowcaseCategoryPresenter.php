<?php

namespace App\Presenter\ShowcaseCategory;

use App\Repository\Showcase\ShowcaseRepository;
use App\Repository\ShowcaseCategoryRepository;

class ShowcaseCategoryPresenter
{
    public function __construct(
        private ShowcaseRepository  $showcaseRepository,
        private ShowcaseCategoryRepository $showcaseCategoryRepository,
    ) {
    }

    public function getTree($showcaseId): array|string
    {
        $showcase = $this->showcaseRepository->get($showcaseId);
        return $this->showcaseCategoryRepository->getTree($showcase);
    }
}