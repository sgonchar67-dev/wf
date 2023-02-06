<?php

namespace App\Domain\Entity\User;

interface UserPermissionConstants
{
    public const COMPANY = 'COMPANY';
    public const CONTRACTORS = 'CONTRACTORS';
    public const EMPLOYERS = 'EMPLOYERS';
    public const MESSAGES = 'MESSAGES';
    public const PRODUCTS = 'PRODUCTS';
    public const PURCHASES = 'PURCHASES';
    public const REFERENCE_BOOKS = 'REFERENCE_BOOKS';
    public const SALES = 'SALES';
    public const SHOWCASE = 'SHOWCASE';

    public const DEFAULT_PERMISSIONS = [
        self::COMPANY => true,
        self::CONTRACTORS => true,
        self::EMPLOYERS => true,
        self::MESSAGES => true,
        self::PRODUCTS => true,
        self::PURCHASES => true,
        self::REFERENCE_BOOKS => true,
        self::SALES => true,
        self::SHOWCASE => true,
    ];

    public const DEFAULT_PERMISSIONS_NAME = 'Администратор';
}
