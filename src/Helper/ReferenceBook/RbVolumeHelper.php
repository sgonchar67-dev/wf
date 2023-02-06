<?php

namespace App\Helper\ReferenceBook;

use App\Domain\Entity\ReferenceBook\ReferenceBookValue;
use JetBrains\PhpStorm\Pure;

class RbVolumeHelper {
	const VOLUME_ML_ID = 11; //мл
	const VOLUME_L_ID = 9; //л
	const VOLUME_M3_ID = 10; //м3
	const VOLUME_DM3_ID = 13; //дм3
	const VOLUME_SM3_ID = 12; //см3
	const VOLUME_MM3_ID = 36; //мм3

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
            self::VOLUME_ML_ID, self::VOLUME_SM3_ID => $fromValue * 0.000001,
            self::VOLUME_L_ID, self::VOLUME_DM3_ID => $fromValue * 0.001,
            self::VOLUME_MM3_ID => $fromValue * 0.000000001,
            self::VOLUME_M3_ID => $fromValue,
            default => 0,
        };
	}

	#[Pure] public static function convert(int $fromMeasure, float $fromValue, int $toMeasure): float
    {
		$defaultValue = self::conversionToDefaultFromRbvMeasureId($fromMeasure, $fromValue);
        return match ($toMeasure) {
            self::VOLUME_ML_ID, self::VOLUME_SM3_ID => $defaultValue * 1000000,
            self::VOLUME_L_ID, self::VOLUME_DM3_ID => $defaultValue * 1000,
            self::VOLUME_MM3_ID => $fromValue * 1000000000,
            self::VOLUME_M3_ID => $defaultValue,
            default => 0,
        };
	}
}
