<?php

namespace App\Interfaces;

use Symfony\Component\HttpFoundation\File\File;

interface FileUploadInterface
{
    public function getUploadedAt(): \DateTimeInterface;

    public function setUploadedAt(?\DateTimeInterface $dateTime = null): self;

    public function getContentUrl(): ?string;

    public function getFile(): ?File;

    public function getFilePath(): ?string;
    
    public function setFilePath(?string $filePath): self;
}