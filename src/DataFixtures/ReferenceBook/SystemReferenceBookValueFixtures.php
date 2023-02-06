<?php

namespace App\DataFixtures\ReferenceBook;

use App\DataFixtures\FixtureReferenceTrait;
use App\Domain\Entity\ReferenceBook\ReferenceBook;
use App\Domain\Entity\ReferenceBook\ReferenceBookValue;
use App\Helper\ObjectHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SystemReferenceBookValueFixtures extends Fixture
{
    use FixtureReferenceTrait;

    private const DATA = [
        ReferenceBook::RB_ID_DELIVERY => [],
        ReferenceBook::RB_ID_PAYMENT => [],
        ReferenceBook::RB_ID_MEASURE => [
            ['id' => ReferenceBookValue::ID_PC, 'value' => 'шт'],
        ],
        ReferenceBook::RB_ID_WEIGHT => [
            ['id' => ReferenceBookValue::ID_GRAM, 'value' => 'г'],
        ],
        ReferenceBook::RB_ID_VOLUME => [
            ['id' => ReferenceBookValue::ID_LITRE, 'value' => 'л'],
        ],
        ReferenceBook::RB_ID_PACK_RB => [],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DATA as $rbId => $valuesData) {
            $referenceBook = $this->getReferenceBook($manager, $rbId);
            foreach ($valuesData as $data) {
                $id = $data['id'];
                if (!$referenceBookValue = $manager->find(ReferenceBookValue::class, $id)) {
                    $referenceBookValue = $this->create($data, $referenceBook);
                    $manager->persist($referenceBookValue);
                }

                $this->addReferenceWithId($referenceBookValue,SystemReferenceBookFixtures::class, $id);
            }

        }

        $manager->flush();
    }

    private function create(array $data, ReferenceBook $book): ReferenceBookValue
    {
        $referenceBookValue = new ReferenceBookValue($book, $data['value']);
        ObjectHelper::setId($referenceBookValue, $data['id']);
        return $referenceBookValue;
    }

    private function getReferenceBook(ObjectManager $manager, $rbId): ReferenceBook
    {
        if (!$referenceBook = $manager->find(ReferenceBook::class, $rbId)) {
            $referenceBook = $this->getReferenceWithId(SystemReferenceBookFixtures::class, $rbId);
        }
        return $referenceBook;
    }
}
