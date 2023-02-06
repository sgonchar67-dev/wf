<?php

namespace App\Service\Moysklad;

use App\Repository\Contractor\ContractorRepository;
use App\Repository\Moysklad\MoyskladContractorRepository;

class MoyskladContractorService
{
    public function __construct(
        private MoyskladContractorRepository $moyskladContractorRepository,
        private ContractorRepository $contractorRepository,
    ) {
    }

    public function create($contractorId, $msContractorId): \App\Domain\Entity\Moysklad\Contractor\MoyskladContractor
    {
        $contractor = $this->contractorRepository->get($contractorId);
        $msContractor = new \App\Domain\Entity\Moysklad\Contractor\MoyskladContractor($msContractorId, $contractor);
        $this->moyskladContractorRepository->save($msContractor);
        return $msContractor;
    }

    public function createByDto($contractorId, $msContractorId): \App\Domain\Entity\Moysklad\Contractor\MoyskladContractor
    {
        $contractor = $this->contractorRepository->get($contractorId);
        $msContractor = new \App\Domain\Entity\Moysklad\Contractor\MoyskladContractor($msContractorId, $contractor);
        $this->moyskladContractorRepository->save($msContractor);
        return $msContractor;
    }
}