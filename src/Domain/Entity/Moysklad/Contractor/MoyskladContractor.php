<?php

namespace App\Domain\Entity\Moysklad\Contractor;

use App\Domain\Entity\Contractor\Contractor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class MoyskladContractor
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private string $id;

    #[ORM\OneToOne(targetEntity: Contractor::class)]
    private Contractor $contractor;

    /**
     * @param Uuid $id
     * @param Contractor $contractor
     */
    public function __construct(string $id, Contractor $contractor)
    {
        $this->id = $id;
        $this->contractor = $contractor;
    }


}