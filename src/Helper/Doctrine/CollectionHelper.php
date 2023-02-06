<?php

namespace App\Helper\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ReflectionClass;

class CollectionHelper
{
    public static function create(Collection|array|null $items, $owner = null): Collection|ArrayCollection
    {
        $items = $items ?? [];
        $collection = is_array($items) ? new ArrayCollection($items) : $items;

        if ($owner && $collection->first()) {
            $ref = new ReflectionClass($owner);
            if (method_exists($collection->first(), $method = "set{$ref->getShortName()}")) {
                $collection->forAll(fn($i, $item) => $item->$method($owner));
            }
        }
        return $collection;
    }

    public static function findOneById(Collection|ArrayCollection|array $collection, $id)
    {
        return self::create($collection)
            ->matching(CriteriaHelper::createIdCriteria($id))
            ->first() ?: null;
    }

    public static function addItems(Collection $collection, Collection|array $items): Collection
    {
        foreach (self::create($items) as $item) {
            self::addItem($collection, $item);
        }

        return $collection;
    }


    public static function addItem(Collection $collection, $item): Collection
    {
        if (!$collection->contains($item)) {
            $collection->add($item);
        }

        return $collection;
    }
}