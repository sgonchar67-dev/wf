<?php
namespace App\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Product\Product;
use App\Domain\Entity\User\Profile;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermission;
use App\Domain\Entity\User\UserPermissionTemplate;
use App\Domain\Entity\User\UserRolesConstants;
use App\Security\SecurityInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private const RESOURCE_CLASSES = [
        Product::class,
        User::class,
        UserPermission::class,
        Profile::class,
        Employee::class,
        UserPermissionTemplate::class,
    ];

    private ?Request $request;

    public function __construct(private SecurityInterface $security, RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []): void
    {
        //$this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        /** @var User|null $user */
        $user = $this->security->getUser();
        $apiSubresourceOperationName = $this->request->attributes->get('_api_subresource_operation_name');
        if (
            !in_array($resourceClass, self::RESOURCE_CLASSES)
            || $apiSubresourceOperationName === 'api_showcases_products_get_subresource'
            || $this->security->isGranted(UserRolesConstants::ROLE_SUPER_ADMIN)
            || null === $user
        ) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        if ($resourceClass === Employee::class) {
            if ($apiSubresourceOperationName === 'api_companies_employees_get_subresource') {
                $queryBuilder->innerJoin(sprintf('%s.user', $rootAlias), 'user');
                $rootAlias = 'user';
                $queryBuilder->andWhere(sprintf('%s.roles', $rootAlias) . ' NOT LIKE :user_role')
                    ->setParameter('user_role', '%' . User::ROLE_OWNER . '%');
            }
            return;
        }

        if ($resourceClass === UserPermissionTemplate::class) {
            $queryBuilder->andWhere(sprintf('%s.company', $rootAlias) . ' = :company')
                ->setParameter('company', $user->getCompany() ?? $user->getEmployeeCompany());
            return;
        }

        if ($resourceClass === Product::class) {
            $queryBuilder->leftJoin(sprintf('%s.company', $rootAlias), 'company');
            $rootAlias = 'company';
            if ($this->security->isGranted('ROLE_USER')) {
                $queryBuilder->leftJoin(sprintf('%s.employees', $rootAlias), 'employees');
                $rootAlias = 'employees';
            }
        }

        if (in_array($resourceClass, [UserPermission::class, User::class, Profile::class])) {
            if (in_array($resourceClass, [UserPermission::class, Profile::class])) {
                $queryBuilder->innerJoin(sprintf('%s.user', $rootAlias), 'user');
                $rootAlias = 'user';
            }
            $queryBuilder->innerJoin(sprintf('%s.employee', $rootAlias), 'employee')
                ->innerJoin('employee.company', 'company');
            $rootAlias = 'company';
        }


        $queryBuilder->andWhere(sprintf('%s.user', $rootAlias) . ' = :current_user')
            ->setParameter('current_user', $user->getId());
    }
}