<?php

namespace App\Domain\ValueObject;

use App\Helper\PhoneHelper;

class Username
{
    private string $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function isEmail(): bool
    {
        return str_contains($this->value, '@');
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->isEmail() ? $this->value : PhoneHelper::format($this->value);
    }
}