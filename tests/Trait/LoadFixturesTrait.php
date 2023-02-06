<?php

namespace App\Tests\Trait;

use App\Tests\FixturesLoader;

trait LoadFixturesTrait
{
    protected static bool $isPurgerEnabled = false;
    /**
     * @param array<int|string,string> $fixtures
     */
    public function loadFixtures(array $fixtures): void
    {
        $loader = new FixturesLoader($this->getContainer(), static::$isPurgerEnabled);
        $loader->load($fixtures);
    }

    public static function enablePurger(): void
    {
        self::$isPurgerEnabled = true;
    }
}