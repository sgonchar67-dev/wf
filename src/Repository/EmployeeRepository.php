<?php

namespace App\Repository;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Employee get($id)
 * @method Employee|null find($id)
 * @method persist(Employee $entity)
 * @method save(Employee $entity)
 */
class EmployeeRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(Employee::class);
    }
}