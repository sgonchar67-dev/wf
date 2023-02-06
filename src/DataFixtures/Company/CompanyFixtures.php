<?php

namespace App\DataFixtures\Company;

use App\DataFixtures\User\SuperAdminFixtures;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CompanyFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference(SuperAdminFixtures::class);
        $company = new Company($user, 'Company name');
        $manager->persist($company);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SuperAdminFixtures::class,
        ];
    }
}
