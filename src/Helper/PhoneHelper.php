<?php

namespace App\Helper;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneHelper
{
    public static function format(?string $phone, $code = null): ?string
    {
        if (!$phone) {
            return null;
        }
        $isDev = getenv('APP_ENV') === 'dev';
        $util = PhoneNumberUtil::getInstance();
        $phone = ltrim($phone, '+');
        $phone = $code ? "+{$code}{$phone}" : $phone;
        $region = $util->getRegionCodeForCountryCode($code ?: '7');
        $phoneNumber = $util->parse($phone, $region);
        if ($phoneNumber && !$util->isValidNumber($phoneNumber) && !$isDev) {
            throw new \DomainException('Incorrect phone number', 400);
        }
        return substr(
            $util->format($phoneNumber, PhoneNumberFormat::E164),
            1
        ) ?: null;
    }
}