<?php

namespace App\DataFixtures\Product;

use App\DataFixtures\Company\CompanyFixtures;
use App\DataFixtures\FixtureReferenceTrait;
use App\DataFixtures\ReferenceBook\SystemReferenceBookValueFixtures;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Product\Product;
use App\Domain\Entity\ReferenceBook\ReferenceBookValue;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    use FixtureReferenceTrait;

    public function load(ObjectManager $manager): void
    {

        /** @var Company $company */
        $company = $this->getReference(CompanyFixtures::class);
        $product = new Product(
            'Product name',
            $company,
            $this->getDefaultMeasure(),
            $this->getDefaultWeightMeasure(),
            $this->getDefaultVolumeMeasure()
        );
        $manager->persist($product);

        $manager->flush();
    }

    private function getDefaultMeasure(): ReferenceBookValue
    {
        /** @var ReferenceBookValue $measure */
        $measure = $this->getReferenceWithAttribute(SystemReferenceBookValueFixtures::class, ReferenceBookValue::ID_PC);
        return $measure;
    }

    private function getDefaultWeightMeasure(): ReferenceBookValue
    {
        /** @var ReferenceBookValue $measure */
        $measure = $this->getReferenceWithAttribute(SystemReferenceBookValueFixtures::class, ReferenceBookValue::ID_GRAM);
        return $measure;
    }

    private function getDefaultVolumeMeasure(): ReferenceBookValue
    {
        /** @var ReferenceBookValue $measure */
        $measure = $this->getReferenceWithAttribute(SystemReferenceBookValueFixtures::class, ReferenceBookValue::ID_LITRE);
        return $measure;
    }

    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class,
            SystemReferenceBookValueFixtures::class,
        ];
    }
}
