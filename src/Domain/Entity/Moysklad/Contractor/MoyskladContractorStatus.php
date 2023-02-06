<?php

namespace App\Domain\Entity\Moysklad\Contractor;

use App\Domain\Entity\Contractor\ContractorStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class MoyskladContractorStatus
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private string $id;

    #[ORM\OneToOne(targetEntity: ContractorStatus::class)]
    private \App\Domain\Entity\Contractor\ContractorStatus $contractorStatus;


    public function __construct(string $id, \App\Domain\Entity\Contractor\ContractorStatus $contractor)
    {
        $this->id = $id;
        $this->contractorStatus = $contractor;
    }
}