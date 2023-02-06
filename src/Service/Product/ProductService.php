<?php

namespace App\Service\Product;

use App\Domain\Entity\Document;
use App\Domain\Entity\Image;
use App\Domain\Entity\Product\Product;
use App\Domain\Entity\Product\ProductPackage;
use App\Interfaces\FileUploadInterface;
use App\Repository\Product\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vich\UploaderBundle\Storage\StorageInterface;

class ProductService 
{
    public function __construct(
        private ProductRepository $productRepository,
        private StorageInterface $storageInterface,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function copyProduct(int $idFrom): ?\App\Domain\Entity\Product\Product
    {
        /** @var \App\Domain\Entity\Product\Product|null $product  */
        $product = $this->productRepository->get($idFrom);

        $newProduct = clone $product;
        $newProduct->setName($newProduct->getName() . ' (копия)');

        foreach ($product->getImages() as $image) {
            /** @var \App\Domain\Entity\Image $newImage */
            $newImage = $this->copyFileEntity($image);
            $newProduct->addImage($newImage);
        }
        foreach ($product->getDocuments() as $document) {
            /** @var \App\Domain\Entity\Document $newDocument */
            $newDocument = $this->copyFileEntity($document);
            $newProduct->addDocument($newDocument);
        }
        $this->productRepository->save($newProduct);
        return $newProduct;
    }

    public function deleteProductPackage(\App\Domain\Entity\Product\ProductPackage $deletePackage): ?\App\Domain\Entity\Product\ProductPackage
    {
        try {
            $this->entityManager->remove($deletePackage);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            if (!$this->entityManager->isOpen()) {
                $this->entityManager = $this->entityManager->create(
                    $this->entityManager->getConnection(),
                    $this->entityManager->getConfiguration()
                );
            }
            $deletePackage = $this->entityManager->find(\App\Domain\Entity\Product\ProductPackage::class, $deletePackage->getId());
            $deletePackage->setArchived(true);
            $this->entityManager->persist($deletePackage);
            $this->entityManager->flush();

            return $deletePackage;
        }
        return null;
    }

    private function copyFileEntity(FileUploadInterface $origin, string $properyName = 'file'): FileUploadInterface
    {
        $new = clone $origin;
        $new->setUploadedAt();

        $originFileLocation = $this->storageInterface->resolvePath($origin, $properyName); // from
        if (!file_exists($originFileLocation)) {
            throw new NotFoundHttpException("Origin file \"{$originFileLocation}\" not found");
        }
        $newFileLocation = $this->storageInterface->resolvePath($new, $properyName);
        $newFilePath = \str_ireplace($new->getFilePath(), '', $newFileLocation);
        $fileNameWithoutHash = \preg_replace('/-[a-f\d]*\./m', '.', $new->getFilePath());
        $newExtension = \strtolower(\pathinfo($fileNameWithoutHash, \PATHINFO_EXTENSION));
        $newBasename = \pathinfo($fileNameWithoutHash, \PATHINFO_FILENAME);
        $uniqId = \str_replace('.', '', \uniqid('-', true));
        $newFileName = \sprintf('%s%s.%s', $newBasename, $uniqId, $newExtension);
        $newFile = \sprintf('%s%s', $newFilePath, $newFileName);
        if (!\file_exists($newFilePath) &&
            !\mkdir($newFilePath, 0777, true) &&
            !\is_dir($newFilePath)
        ) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $newFilePath));
        }

        if (!\copy($originFileLocation, $newFile)) {
            throw new \RuntimeException("File was not copied");
        }
        $new->setFilePath($newFileName);

        return $new;
    }
}