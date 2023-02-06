<?php

namespace App\Domain\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreateImageAction;
use App\Interfaces\FileUploadInterface;
use App\Repository\ImageRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'controller' => CreateImageAction::class,
            'deserialize' => false,
            'validation_groups' => ['Default', 'image_create'],
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    iri: 'http://schema.org/Image',
    itemOperations: ['get', 'delete'],
    normalizationContext: ['groups' => ['image:read']]
)]

class Image implements FileUploadInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['image:read'])]
    private ?int $id = null;

    #[ApiProperty(iri: 'http://schema.org/contentUrl')]
    #[Groups(['image:read', 'Order:read', 'OrderEventLog:read'])]
    public ?string $contentUrl = null;

    /**
     * @Vich\UploadableField(mapping="image", fileNameProperty="filePath")
     */
    #[Vich\UploadableField(mapping: 'image', fileNameProperty: 'filePath')]
    #[Assert\NotNull(groups: ['image_create'])]
    #[Assert\File(maxSize: "5120K", mimeTypes: ['image/jpeg', 'image/gif', 'image/png'])]
    public ?File $file = null;

    #[ORM\Column(nullable: true)]
    public ?string $filePath = null;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $uploadedAt;

    public function __construct()
    {
        $this->uploadedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUploadedAt(): DateTimeInterface
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(?DateTimeInterface $dateTime = null): self
    {
        $this->uploadedAt = ($dateTime === null ? new \DateTime() : $dateTime); 
        return $this;
    }

    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    public function setContentUrl(?string $contentUrl): self
    {
        $this->contentUrl = $contentUrl;
        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }
}
