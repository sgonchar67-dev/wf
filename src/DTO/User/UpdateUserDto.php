<?php

namespace App\DTO\User;


use Symfony\Component\Serializer\Annotation\Groups;

class UpdateUserDto
{
    #[Groups(['User:update'])]
    public ?string $phone = null;
    #[Groups(['User:update'])]
    public ?string $password = null;
    #[Groups(['User:update'])]
    public ?string $email = null;
    #[Groups(['User:update'])]
    public ?string $newPassword = null;
}