<?php

namespace App\Presenter\ResourceCategory;

use App\Repository\Company\CompanyRepository;
use App\Repository\ResourceCategoryRepository;

class ResourceCategoryPresenter
{
    public function __construct(
        private CompanyRepository  $companyRepository,
        private ResourceCategoryRepository $resourceCategoryRepository,
    ) {
    }

    public function getTree($companyId): array|string
    {
        $company = $this->companyRepository->get($companyId);
        return $this->resourceCategoryRepository->getTree($company);
    }
}