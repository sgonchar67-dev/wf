<?php

namespace App\Serializer;

use App\Domain\Entity\Product\Product;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

final class ProductDenormalizer implements DenormalizerAwareInterface, ContextAwareDenormalizerInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'PRODUCT_DENORMALIZER_ALREADY_CALLED';

    public function denormalize($data, string $type, $format = null, array $context = []): mixed
    {
        $context[self::ALREADY_CALLED] = true;
        $data['rbvMeasure'] = $data['rbvMeasure'] ?? '/api/reference_book_values/' . Product::RBV_MEASURE_ID_DEFAULT;
        $data['rbvWeightMeasure'] = $data['rbvWeightMeasure'] ?? '/api/reference_book_values/' . Product::RBV_WEIGHT_MEASURE_ID_DEFAULT;
        $data['rbvVolumeMeasure'] = $data['rbvVolumeMeasure'] ?? '/api/reference_book_values/' . Product::RBV_VOLUME_MEASURE_ID_DEFAULT;
        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }
        return $type === Product::class;
    }
}