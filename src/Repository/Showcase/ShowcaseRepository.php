<?php

namespace App\Repository\Showcase;

use App\Domain\Entity\Showcase\Showcase;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Showcase get($id)
 * @method Showcase|null find($id)
 * @method remove(\App\Domain\Entity\Showcase\Showcase $entity)
 * @method delete(\App\Domain\Entity\Showcase\Showcase $entity)
 * @method persist(Showcase $entity)
 * @method save(Showcase $entity)
 */
class ShowcaseRepository  extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $this->entityManager->getRepository(\App\Domain\Entity\Showcase\Showcase::class);
    }
}