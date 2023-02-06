<?php

namespace App\Controller;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\User\User;
use App\Exception\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

class AbstractController extends SymfonyAbstractController
{
    /**
     * @return User|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getUser(): ?UserInterface
    {
        /** @var TokenStorageInterface $tokenStorage */
        $tokenStorage = $this->container->get('security.token_storage');
        /** @var User $user */
        $user = $tokenStorage->getToken()?->getUser();
        if ($user && !$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }
        return $user;
    }

    protected function getEmployee(): Employee
    {
        if (!$employee = $this->getUser()?->getEmployee()) {
            throw NotFoundException::create(Employee::class);
        }

        return $employee;
    }
}