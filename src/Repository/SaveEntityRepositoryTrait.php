<?php

namespace App\Repository;

use App\Helper\ObjectHelper;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;

trait SaveEntityRepositoryTrait
{
    #[Pure] protected function getEntityManager(): EntityManagerInterface
    {
        return $this->_em ?? $this->entityManager;
    }

    public function save($entity)
    {
        if (!$this->isPersisted($entity)) {
            $this->persist($entity);
        }
        $this->getEntityManager()->flush();
    }

    private function isPersisted(object $entity): bool
    {
        return ObjectHelper::isPrivatePropertySet($entity, 'id');
    }
}