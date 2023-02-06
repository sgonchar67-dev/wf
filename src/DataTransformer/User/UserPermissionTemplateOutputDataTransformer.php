<?php
namespace App\DataTransformer\User;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\User\UserPermissionTemplateOutputDto;
use App\Domain\Entity\User\UserPermissionTemplate;
use App\Service\User\UserPermissionService;

final class UserPermissionTemplateOutputDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private UserPermissionService $userPermissionService,
        private IriConverterInterface $iri
    ) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function transform($object, string $to, array $context = []): object
    {
        /** @var UserPermissionTemplate $template */
        $template = $object;
        $newOutput = new UserPermissionTemplateOutputDto();
        $newOutput
            ->setId($template->getId())
            ->setCompany($this->iri->getIriFromItem($template->getCompany()))
            ->setDescription($template->getDescription())
            ->setTemplatePermission($template->getTemplatePermission());
        
        if ($context['operation_type'] === 'collection')
            $newOutput->setEmployees($this->getItemsIri($this->userPermissionService->getEmployeesAttachedTemplate($template)));

        if ($context['operation_type'] === 'item')
            $newOutput->setEmployees($this->userPermissionService->getEmployeesAttachedTemplate($template));

        return $newOutput;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return UserPermissionTemplateOutputDto::class === $to && $data instanceof \App\Domain\Entity\User\UserPermissionTemplate;
    }

    private function getItemsIri(array $items): array
    {
        $result = [];
        foreach ($items as $item) {
            $result[] = $this->iri->getIriFromItem($item);
        }
        return $result;
    }
}