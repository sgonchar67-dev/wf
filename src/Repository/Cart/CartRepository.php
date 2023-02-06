<?php

namespace App\Repository\Cart;

use App\Domain\Entity\Company\Employee;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Entity\Cart\Cart;
use App\Domain\Entity\Showcase\Showcase;
use App\Exception\NotFoundException;

class CartRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(\App\Domain\Entity\Cart\Cart::class);
    }

    public function findOneByEmployeeAndShowcase(Employee $employee, Showcase $showcase): ?Cart
    {
        /** @var \App\Domain\Entity\Cart\Cart|null $cart */
        return $this->repo->findOneBy([
            'employee' => $employee,
            'showcase' => $showcase,
            'isClosed' => false,
        ]);
    }

    public function getByCompanyAndShowcase(Employee $employee, Showcase $showcase): Cart
    {
        if(!$cart = $this->findOneByEmployeeAndShowcase($employee, $showcase)) {
            $employeeId = $employee->getId();
            $showcaseId = $showcase->getId();
            throw NotFoundException::create(Cart::class, [
                $employeeId => $employee->getId(),
                $showcaseId => $showcase->getId()
            ]);
        }

        return $cart;
    }

    public function find($id): ?\App\Domain\Entity\Cart\Cart
    {
        /** @var \App\Domain\Entity\Cart\Cart|null $cart */
        $cart = $this->repo->find($id);
        return $cart;
    }

    /**
     * @param $id
     * @return \App\Domain\Entity\Cart\Cart
     * @throws \App\Exception\NotFoundException
     */
    public function get($id): \App\Domain\Entity\Cart\Cart
    {
        if(!$cart = $this->find($id)) {
            throw new NotFoundException("Cart {$id} not found");
        }

        return $cart;
    }
}