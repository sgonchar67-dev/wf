<?php

namespace App\Domain\Entity\User;

use App\Domain\Entity\User\UserPermissionConstants;
use App\Domain\Entity\User\UserRolesConstants;

interface UserPermissionToRoleMap
{
    public const PERMISSION_TO_ROLE_MAP = [
        UserPermissionConstants::COMPANY => UserRolesConstants::ROLE_ADMIN_COMPANY,
        UserPermissionConstants::CONTRACTORS => UserRolesConstants::ROLE_ADMIN_CONTRACTORS,
        UserPermissionConstants::EMPLOYERS => UserRolesConstants::ROLE_ADMIN_EMPLOYERS,
        UserPermissionConstants::MESSAGES => UserRolesConstants::ROLE_ADMIN_MESSAGES,
        UserPermissionConstants::PRODUCTS => UserRolesConstants::ROLE_ADMIN_PRODUCTS,
        UserPermissionConstants::PURCHASES => UserRolesConstants::ROLE_ADMIN_PURCHASES,
        UserPermissionConstants::SALES => UserRolesConstants::ROLE_ADMIN_SALES,
        UserPermissionConstants::REFERENCE_BOOKS => UserRolesConstants::ROLE_ADMIN_REFERENCE_BOOKS,
        UserPermissionConstants::SHOWCASE => UserRolesConstants::ROLE_ADMIN_SHOWCASE,
    ];
}