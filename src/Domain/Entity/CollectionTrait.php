<?php

namespace App\Domain\Entity;

use App\Helper\Doctrine\CollectionHelper;
use Doctrine\Common\Collections\Collection;

trait CollectionTrait
{
    private function createCollection(Collection|array $items = []): Collection
    {
        return CollectionHelper::create($items, $this);
    }

    private function addItem(Collection $collection, $item): self
    {
        CollectionHelper::addItem($collection, $item);
        return $this;
    }
}