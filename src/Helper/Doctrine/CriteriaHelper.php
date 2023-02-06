<?php

namespace App\Helper\Doctrine;

use Doctrine\Common\Collections\Criteria;

class CriteriaHelper
{
    public static function createIdCriteria($id): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('id', $id));
    }
}