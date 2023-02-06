<?php

namespace App\DataFixtures\ReferenceBook;

use App\Domain\Entity\ReferenceBook\ReferenceBook;
use App\Helper\ObjectHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SystemReferenceBookFixtures extends Fixture
{
    private const DATA = [
        ReferenceBook::RB_ID_DELIVERY => ['name' => 'Доставка'],
        ReferenceBook::RB_ID_PAYMENT => ['name' => 'Оплата'],
        ReferenceBook::RB_ID_MEASURE => ['name' => 'Единица измерения'],
        ReferenceBook::RB_ID_WEIGHT => ['name' => 'Вес'],
        ReferenceBook::RB_ID_VOLUME => ['name' => 'Объем'],
        ReferenceBook::RB_ID_PACK_RB => ['name' => 'Вид упаковки'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DATA as $data) {
            if (!$referenceBook = $manager->find(ReferenceBook::class, $data['id'])) {
                $referenceBook = $this->create($data);
                $manager->persist($referenceBook);
            }

            $this->addReference(SystemReferenceBookFixtures::class . $data['id'], $referenceBook);
        }

        $manager->flush();
    }

    private function create(array $data): ReferenceBook
    {
        $referenceBook = new ReferenceBook($data['name'], null);
        ObjectHelper::setId($referenceBook, $data['id']);
        $referenceBook->setSystem(true);
        return $referenceBook;
    }
}
