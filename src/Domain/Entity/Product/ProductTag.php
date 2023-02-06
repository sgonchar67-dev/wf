<?php

namespace App\Domain\Entity\Product;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Entity\Tag;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity]
class ProductTag extends Tag
{

}
