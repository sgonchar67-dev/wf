<?php

namespace App\Domain\Entity\User;

interface UserRolesConstants
{
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public const ROLE_OWNER = 'ROLE_OWNER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_USER = 'ROLE_USER';

    public const ROLE_ADMIN_COMPANY = 'ROLE_ADMIN_COMPANY';
    public const ROLE_ADMIN_SHOWCASE = 'ROLE_ADMIN_SHOWCASE';
    public const ROLE_ADMIN_PRODUCTS = 'ROLE_ADMIN_PRODUCTS';
    public const ROLE_ADMIN_REFERENCE_BOOKS = 'ROLE_ADMIN_REFERENCE_BOOKS';
    public const ROLE_ADMIN_CONTRACTORS = 'ROLE_ADMIN_CONTRACTORS';
    public const ROLE_ADMIN_EMPLOYERS = 'ROLE_ADMIN_EMPLOYERS';
    public const ROLE_ADMIN_MESSAGES = 'ROLE_ADMIN_MESSAGES';
    public const ROLE_ADMIN_SALES = 'ROLE_ADMIN_SALES';
    public const ROLE_ADMIN_PURCHASES = 'ROLE_ADMIN_PURCHASES';
}