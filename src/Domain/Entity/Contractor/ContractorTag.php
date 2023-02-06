<?php

namespace App\Domain\Entity\Contractor;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Entity\Tag;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity]
class ContractorTag extends Tag
{

}
