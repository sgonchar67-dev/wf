<?php

namespace App\DTO\User;

use App\Domain\Entity\User\User;
use App\Helper\PhoneHelper;
use Symfony\Component\HttpFoundation\Request;

class CreateUserDto
{
    public string $phone;
    public ?string $password = null;
    public ?array $roles = null;
    public ?string $email = null;
    public ?string $profileName = null;

    private bool $isPasswordGenerated = false;

    public static function create(string $phone, ?string $password, ?array $roles, ?string $email, ?string $profileName): CreateUserDto
    {
        $self = new self();
        $self->phone = $phone;
        $self->password = $password;
        $self->roles = $roles;
        $self->email = $email;
        $self->profileName = $profileName;
        return $self;
    }

    public function handleRequest(Request $request): static
    {
        $content = $request->getContent();
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        $phone = $data['phone'];
        $this->phone = PhoneHelper::format($phone);
        $this->password = $data['password'] ?? null;
        $this->roles = $data['roles'] ?? [User::ROLE_OWNER];
        $this->email = $data['email'] ?? null;
        $this->profileName = $data['profileName'] ?? null;

        return $this;
    }

    public function withGeneratedPassword($password): self
    {
        $self = clone $this;
        $self->password = $password;
        $self->isPasswordGenerated = true;
        return $self;
    }

    public function isPasswordGenerated(): bool
    {
        return $this->isPasswordGenerated;
    }


}