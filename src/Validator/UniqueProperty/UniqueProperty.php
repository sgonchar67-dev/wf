<?php

namespace App\Validator\UniqueProperty;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
final class UniqueProperty extends Constraint
{
    public string $message = 'The the properties "{{ properties }}" contain the same value "{{ value }}"';
    public ?string $entity = null;
    public ?string $property = null;

    public function __construct(?string $entity = null, ?string $property = null)
    {
        parent::__construct();
        if ($entity) {
            $this->entity = $entity;
            $this->property = $property ?: 'id';
        }
    }
}