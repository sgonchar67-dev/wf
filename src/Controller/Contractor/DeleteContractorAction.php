<?php

namespace App\Controller\Contractor;

use App\Controller\AbstractController;
use App\Domain\Entity\Contractor\Contractor;
use App\Exception\AccessDeniedException;
use App\Exception\NotFoundException;
use App\Handler\Contractor\DeleteContractorHandler;
use App\Handler\Contractor\UpdateContractorHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class DeleteContractorAction extends AbstractController
{
    public function __invoke(Contractor $data, DeleteContractorHandler $handler)
    {
        try {
            $handler->handle($data, $this->getEmployee());
        } catch (\Exception $e) {
            $this->json($data, JsonResponse::HTTP_OK, ['Access-Control-Allow-Origin' => '*'])->send();
        }
    }
}