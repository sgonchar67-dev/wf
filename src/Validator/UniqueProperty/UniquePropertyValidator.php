<?php

namespace App\Validator\UniqueProperty;

use App\Helper\ObjectHelper;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniquePropertyValidator extends ConstraintValidator
{

    public function __construct(private PropertyAccessorInterface $propertyAccessor)
    {
    }

    /**
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueProperty) {
            throw new UnexpectedTypeException($constraint, UniqueProperty::class);
        }

        if (null === $value) {
            return;
        }

        if ($constraint->entity &&
            !$value instanceof $constraint->entity
        ) {
            throw new UnexpectedValueException($value, $constraint->entity);
        }
        $object = $this->context->getObject();

        $checkedPropertyName = $this->context->getPropertyName();

        foreach (ObjectHelper::getObjectVars($object) as $propertyName => $propertyValue) {
            if ($checkedPropertyName === $propertyName) {
                continue;
            }
            if ($constraint->entity && $constraint->property) {
                $constraintPropertyValue = $this->propertyAccessor->getValue($value, $constraint->property);
                $subObject = $this->propertyAccessor->getValue($object, $propertyName);
                if (!$subObject instanceof $constraint->entity) {
                    continue;
                }
                $subObjectValue = $this->propertyAccessor->getValue($subObject, $constraint->property);
                if ($constraintPropertyValue === $subObjectValue) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ properties }}', implode(', ', [
                            "{$checkedPropertyName}.{$constraint->property}",
                            "{$propertyName}.{$constraint->property}"
                        ]))
                        ->setParameter('{{ value }}', "{$constraintPropertyValue}")
                        ->addViolation();
                    break;
                }
            } else {
                if ($value === $propertyValue) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ properties }}', implode(', ', [$checkedPropertyName, $propertyName]))
                        ->setParameter('{{ value }}', (string) $value)
                        ->addViolation();
                    break;
                }
            }
        }
    }
}