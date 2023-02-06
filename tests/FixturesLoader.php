<?php

namespace App\Tests;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Purger\ORMPurgerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FixturesLoader
{
    public function __construct(
        private ContainerInterface $container,
        private bool $isPurgerEnabled = true,
    ) {
    }

    /**
     * @param array<int|string,string> $fixtures
     */
    public function load(array $fixtures): void
    {
        $loader = new Loader();
        foreach ($fixtures as $class) {
            /** @var AbstractFixture $fixture */
            $fixture = $this->container->get($class);
            $loader->addFixture($fixture);
        }
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $executor = new ORMExecutor($em, $this->getPurger($em));
        $executor->execute($loader->getFixtures());
    }

    private function getPurger(EntityManagerInterface $em): ?ORMPurgerInterface
    {
        return $this->isPurgerEnabled ? new ORMPurger($em) : null;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function withContainer(ContainerInterface $container): FixturesLoader
    {
        return (clone $this)->setContainer($container);
    }

    /**
     * @param ContainerInterface $container
     * @return FixturesLoader
     */
    public function setContainer(ContainerInterface $container): FixturesLoader
    {
        $this->container = $container;
        return $this;
    }
}