<?php

namespace App\Serializer;

use App\Domain\Entity\Company\Company;
use App\Repository\Contractor\ContractorRepository;
use App\Security\SecurityInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class CompanyNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'COMPANY_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        private SecurityInterface    $security,
        private ContractorRepository $contractorRepository,
    ) {
    }

    /**
     * @param \App\Domain\Entity\Company\Company $object
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): float|array|\ArrayObject|bool|int|string|null
    {
        $context[self::ALREADY_CALLED] = true;
        if ($company = $this->security->getUser()?->getEmployeeCompany()) {
            $object->attachedToContractor = $this->contractorRepository->findByContractorCompany($company, $object);
        }

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return
            empty($context[self::ALREADY_CALLED]) &&
            $data instanceof Company;
    }
}