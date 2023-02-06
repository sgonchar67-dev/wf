<?php

namespace App\Service\PhoneConfirmationCode\dto;

use App\Helper\ApiPlatform\IriHelper;
use App\Helper\PhoneHelper;
use App\Helper\RequestHelper;
use DomainException;
use Symfony\Component\HttpFoundation\Request;

class ConfirmedPhone
{
    public string $userId;
    public string $phone;
    public string $code;

    public static function create(): self
    {
        return new self();
    }

    public function handleRequest(Request $request): static
    {
        $data = RequestHelper::getContent($request);
        if (!$resourceId = $data['user'] ?? null) {
            throw new DomainException('user is required');
        }
        $this->code = $data['code'] ?? null;
        $phone = $data['phone'] ?? null;
        $this->phone = PhoneHelper::format($phone);
        $this->userId = IriHelper::parseId($resourceId);
        return $this;
    }
}