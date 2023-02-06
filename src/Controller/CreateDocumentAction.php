<?php
namespace App\Controller;

use App\Domain\Entity\Document;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class CreateDocumentAction extends AbstractController
{
    public function __invoke(Request $request): Document
    {
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $documentObject = new Document();
        $documentObject->file = $uploadedFile;

        return $documentObject;
    }
}