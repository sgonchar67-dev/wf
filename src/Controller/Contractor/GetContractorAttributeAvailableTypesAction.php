<?php

namespace App\Controller\Contractor;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Domain\Entity\Contractor\ContractorAttribute;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetContractorAttributeAvailableTypesAction extends AbstractController
{

    public function __invoke()
    {
        return ContractorAttribute::getAvailableTypes();
    }
}