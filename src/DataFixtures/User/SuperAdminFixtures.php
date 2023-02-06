<?php

namespace App\DataFixtures\User;

use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserRolesConstants;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SuperAdminFixtures extends Fixture
{
    public const USERNAME = '79156338659';
    public const PASSWORD = 'p12300';

    public function load(ObjectManager $manager): void
    {
        $user = User::create(
            self::USERNAME,
            self::PASSWORD,
            'superAdmin@mail.ru',
            'Super Admin',
            [UserRolesConstants::ROLE_SUPER_ADMIN]
        );

        $manager->persist($user);

        $this->addReference($this::class, $user);

        $manager->flush();
    }
}
