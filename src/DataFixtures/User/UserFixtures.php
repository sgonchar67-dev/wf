<?php

namespace App\DataFixtures\User;

use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserRolesConstants;
use App\DTO\User\CreateUserDto;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const USERNAME = '79998886655';
    public const PASSWORD = 'p12300';

    public function __construct(protected UserFactory $userFactory)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->userFactory->create(CreateUserDto::create(
            self::USERNAME,
            self::PASSWORD,
            [UserRolesConstants::ROLE_OWNER],
            'loginUser@mail.ru',
            'Login User'
        ));

        $manager->persist($user);
        $this->addReference(UserFixtures::class, $user);
        $manager->flush();
    }
}
