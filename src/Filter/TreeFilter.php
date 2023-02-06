<?php
namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class TreeFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $property = str_replace('tree_', '', $property);
        if (
            !$this->isPropertyEnabled($property, $resourceClass) ||
            !$this->isPropertyMapped($property, $resourceClass)
        ) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $field = $property;

        if ($this->isPropertyNested($property, $resourceClass)) {
            [$alias, $field] = $this->addJoinsForNestedProperty($property, $alias, $queryBuilder, $queryNameGenerator, $resourceClass);
        }

        $propertyParts = $this->splitPropertyParts($property, $resourceClass);
        $metadata = $this->getNestedMetadata($resourceClass, $propertyParts['associations']);

        $valueParameter = $queryNameGenerator->generateParameterName($field);

        $childrenIds = $this->getTreeIds((int) $value, $metadata->getName(), $queryBuilder);

        $queryBuilder
                ->andWhere(sprintf('%s.%s IN (:%s)', $alias, $field, $valueParameter))
                ->setParameter($valueParameter, $childrenIds);
    }

    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description['tree_' . $property] = [
                'property' => $property,
                'type' => Type::BUILTIN_TYPE_INT,
                'required' => false,
                'swagger' => [
                    'description' => 'Filter for Gedmo Tree with children',
                    'name' => 'Custom name to use in the Swagger documentation',
                    'type' => 'Will appear below the name in the Swagger documentation',
                ],
            ];
        }

        return $description;
    }

    /**
     * @param integer $value
     * @param string $className
     * @param QueryBuilder $queryBuilder
     * @return int[]
     */
    private function getTreeIds(int $value, string $className, QueryBuilder $queryBuilder): array
    {
        $treeRepo = $queryBuilder->getEntityManager()->getRepository($className);
        $current = $treeRepo->find($value);

        if ($current === null) {
            throw new \Symfony\Component\HttpFoundation\Exception\BadRequestException("Category id#{$value} in '{$className}' not found!");
        }
        $childrenIds = [$current->getId()];
        $children = $treeRepo->getChildren($current);
        foreach ($children as $child){
            $id = $child->getId();
            $childrenIds[] = $id;
        }
        return $childrenIds;
    }
}
