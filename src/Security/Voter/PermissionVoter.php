<?php

namespace App\Security\Voter;

use App\DTO\Employee\EmployeeCreateDto;
use App\DTO\Employee\EmployeeEditDto;
use App\DTO\User\UserPermissionTemplateInputDto;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Product\ProductPackage;
use App\Domain\Entity\User\Profile;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermission;
use App\Domain\Entity\User\UserPermissionConstants;
use App\Domain\Entity\User\UserPermissionTemplate;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PermissionVoter extends Voter
{
    private const PERMISSION_PRODUCTS = [
        ProductPackage::class
    ];

    private const PERMISSION_EMPLOYERS = [
        User::class, 
        UserPermission::class, 
        Profile::class,
        Employee::class,
        EmployeeCreateDto::class,
        EmployeeEditDto::class,
        UserPermissionTemplate::class,
        UserPermissionTemplateInputDto::class
    ];

    private array $permissionClasses;

    public function __construct(
        private Security $security
    ) {
        $this->permissionClasses = array_merge(self::PERMISSION_EMPLOYERS, self::PERMISSION_PRODUCTS);
    }

    #[Pure] protected function supports(string $attribute, $subject): bool
    {
        if ($subject && !is_object($subject)) {
            return false;
        }

        $permissions = array_keys(UserPermissionConstants::DEFAULT_PERMISSIONS);

        $supportsAttribute = in_array($attribute, $permissions);
        
        $supportsSubject = $this->isObjectInstanceOf($subject, $this->permissionClasses);

        return $supportsAttribute && $supportsSubject;
    }

    /**
     * @param string $attribute
     * @param object $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User|null $user */
        if (!$user = $token->getUser()) {
            return false;
        }
        if ($this->security->isGranted(User::ROLE_OWNER)) { 
            return true; 
        }
        if (!($permissions = $user->getPermission()?->getPermissions())) {
            return false;
        }
        if ($this->security->isGranted(User::ROLE_USER)) {
            if ($this->isObjectInstanceOf($subject, self::PERMISSION_EMPLOYERS)) {
                return $permissions[UserPermission::EMPLOYERS];
            }

            if ($this->isObjectInstanceOf($subject, self::PERMISSION_PRODUCTS)) {
                return $permissions[UserPermission::PRODUCTS];
            }
        }
        return false;
    }

    /**
     * @param object|null $objectOrClassName
     * @param string[] $instances
     * @return boolean
     */
    private function isObjectInstanceOf(null|object $objectOrClassName, array $instances): bool
    {
        foreach ($instances as $instance) {
            if ($objectOrClassName instanceof $instance) {
                return true;
            }
        }
        return false;
    }
}
