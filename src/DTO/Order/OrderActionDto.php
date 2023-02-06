<?php

namespace App\DTO\Order;


use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Document;
use App\Domain\Entity\Order\Order;
use App\Helper\ApiPlatform\IriHelper;
use App\Helper\RequestHelper;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Annotation\Groups;

class OrderActionDto
{
    #[Groups(['Order:action'])]
    public ?string $comment = null;
    #[Groups(['Order:action'])]
    public array $documents = [];

    #[Pure] public static function create(): OrderActionDto
    {
        return new self();
    }


    public function handleRequest(Request $request): self
    {
        $data = RequestHelper::getContent($request);

        $this->comment = $data['comment'] ?? null;

        $documents = $data['documents'] ?? [];
        foreach ($documents as $document) {
            $this->documents[] = IriHelper::parseId($document);
        }

        return $this;
    }


}