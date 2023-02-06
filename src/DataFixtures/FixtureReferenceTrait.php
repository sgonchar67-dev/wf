<?php

namespace App\DataFixtures;

use App\Helper\Fixture\ReferenceHelper;

trait FixtureReferenceTrait
{
    public function refName($ref = null): string
    {
        return $ref ?: $this::class;
    }

    public function withIdRefName($id, $ref = null): string
    {
        return ReferenceHelper::withId($this->refName($ref), $id);
    }

    public function withSerialRefName($number, $ref = null): string
    {
        return ReferenceHelper::withSerial($this->refName($ref), $number);
    }
    public function withAttributeRefName($attribute, $ref = null): string
    {
        return ReferenceHelper::withAttribute($this->refName($ref), $attribute);
    }

    public function getReferenceWithAttribute($ref, $attribute): object
    {
        return $this->getReference($this->withAttributeRefName($attribute, $ref));
    }

    public function getReferenceWithId($ref, $id): object
    {
        return $this->getReference($this->withIdRefName($id, $ref));
    }

    public function getReferenceWithSerial($ref, $number): object
    {
        return $this->getReference($this->withSerialRefName($number, $ref));
    }

    public function addReferenceWithAttribute($object, $ref, $attribute): void
    {
        $this->addReference($this->withAttributeRefName($attribute, $ref), $object);
    }

    public function addReferenceWithId($object, $ref, $id): void
    {
        $this->addReference($this->withIdRefName($id, $ref), $object);
    }

    public function addReferenceWithSerial($object, $ref, $number): void
    {
        $this->addReference($this->withSerialRefName($number, $ref), $object);
    }
}