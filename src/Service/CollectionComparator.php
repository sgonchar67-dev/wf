<?php

namespace App\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CollectionComparator
{
//    use NormalizerAwareTrait;
    /**
     * Diff of two Object Collections
     *
     * @param Collection $collection1
     * @param Collection $collection2
     * @param string[] $fieldsToOmit - fields to omit from comparison
     * @return Collection
     */
    public function diff(
        Collection $collection1,
        Collection $collection2,
        array $fieldsToOmit = ['id']
    ): Collection {
        $diff = array_udiff(
            $collection1->toArray(),
            $collection2->toArray());
//        $diff = array_udiff(
//            $collection1->toArray(),
//            $collection2->toArray(),
//            function (object $obj1, object $obj2) use ($fieldsToOmit) {
//
//                $obj1array = json_decode(
//                    json_encode(
//                        $this->arrayFilterByKeys($this->normalize($obj1), $fieldsToOmit)
//                    ),
//                    true
//                );
//                $obj2array = json_decode(
//                    json_encode(
//                        $this->arrayFilterByKeys($this->normalize($obj2), $fieldsToOmit)
//                    ),
//                    true
//                );
//
//                return strcmp(http_build_query($obj1array), http_build_query($obj2array));
//            }
//        );

        return new ArrayCollection($diff);
    }
//
//    private function normalize(object $object): array
//    {
//        return json_decode($this->normalizer->serialize($object, 'json'), true);
//    }
//
//    private function arrayFilterByKeys(array $array, array $keys): array
//    {
//        $result = [];
//
//        foreach ($array as $name => $value) {
//            if (!in_array($name, $keys)) {
//                if (is_array($value)) {
//                    $result[$name] = $this->arrayFilterByKeys($value, $keys);
//                } else {
//                    $result[$name] = $value;
//                }
//            }
//        }
//
//        return $result;
//    }
}