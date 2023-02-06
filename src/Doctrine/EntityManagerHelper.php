<?php

namespace App\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerHelper
{
    public static function reopen(EntityManagerInterface $entityManager): EntityManagerInterface
    {
        if (!$entityManager->isOpen()) {
            $entityManager = $entityManager->create(
                $entityManager->getConnection(),
                $entityManager->getConfiguration()
            );
        }

        return $entityManager;
    }
}