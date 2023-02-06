<?php

namespace App\Service\Auth;

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

class PasswordGenerator
{
    public function generate(): string
    {
        $generator = new ComputerPasswordGenerator();
        $isDev = getenv('APP_ENV') === 'dev';
        return $isDev
            ? 'p12300'
            : $generator
            ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, false)
            ->setLength(10)
            ->generatePassword()
        ;
    }
}