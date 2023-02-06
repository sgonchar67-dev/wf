<?php

namespace App\Helper\ReferenceBook;

use App\Domain\Entity\ReferenceBook\ReferenceBookValue;
use JetBrains\PhpStorm\Pure;

class RbWeightHelper {
    /** @var int кг */
	const WEIGHT_KG_ID = 16
    /** @var int т */;
	const WEIGHT_T_ID = 18;
    /** @var int ц */
	const WEIGHT_C_ID = 17;
    /** @var int г */
	const WEIGHT_G_ID = 14;
    /** @var int мг */
	const WEIGHT_MG_ID = 15;

    #[Pure] public static function conversionToDefaultFromRbvMeasure(?ReferenceBookValue $fromRbvMeasure, float $fromValue): float
    {
        if (!$fromRbvMeasure) {
            return 0;
        }
        return self::conversionToDefaultFromRbvMeasureId($fromRbvMeasure->getId(), $fromValue);
    }

	public static function conversionToDefaultFromRbvMeasureId(int $fromRbvMeasureId, float $fromValue): float
    {
        return match ($fromRbvMeasureId) {
            self::WEIGHT_T_ID => $fromValue * 1000,
            self::WEIGHT_C_ID => $fromValue * 100,
            self::WEIGHT_G_ID => $fromValue * 0.001,
            self::WEIGHT_MG_ID => $fromValue * 0.000001,
            self::WEIGHT_KG_ID => $fromValue,
            default => 0,
        };
	}

	#[Pure] public static function convert(int $fromMeasure, float $fromValue, int $toMeasure): float
    {
	    $defaultValue = self::conversionToDefaultFromRbvMeasureId($fromMeasure, $fromValue);
        return match ($toMeasure) {
            self::WEIGHT_T_ID => $defaultValue * 0.001,
            self::WEIGHT_C_ID => $defaultValue * 0.01,
            self::WEIGHT_G_ID => $defaultValue * 1000,
            self::WEIGHT_MG_ID => $defaultValue * 1000000,
            self::WEIGHT_KG_ID => $defaultValue,
            default => 0,
        };
	}
}
