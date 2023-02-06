<?php

namespace App\Domain\Entity\User;

use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermissionToRoleMap;

trait UserPermissionToRoleMapTrait
{
    private function getPermissionRoles(): array
    {
        $userPermissions = array_filter($this->permission?->getPermissions() ?? []);
        return array_map(
            fn($p) => UserPermissionToRoleMap::PERMISSION_TO_ROLE_MAP[$p] ?? User::ROLE_USER,
            array_keys($userPermissions)
        );
    }
}