<?php

namespace App\Domain\Entity\User;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use App\Domain\Entity\User\User;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToOne;
use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Domain\Entity\Image;

#[Entity]
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => "is_granted('ROLE_OWNER')"],
    ],
    itemOperations: [
        'get' => ['security' => "((is_granted('ROLE_OWNER') or (is_granted('ROLE_USER') and is_granted('EMPLOYERS', object)))
                    and user.getEmployeeCompany() == object.getUser().getEmployeeCompany()) or object.getUser() == user"],
        'put' => ['security' => "((is_granted('ROLE_OWNER') or (is_granted('ROLE_USER') and is_granted('EMPLOYERS', object)))
                    and user.getEmployeeCompany() == object.getUser().getEmployeeCompany()) or object.getUser() == user"],
        'patch' => ['security' => "((is_granted('ROLE_OWNER') or (is_granted('ROLE_USER') and is_granted('EMPLOYERS', object)))
                    and user.getEmployeeCompany() == object.getUser().getEmployeeCompany()) or object.getUser() == user"]
    ],
    denormalizationContext: ['groups' => ['Profile', 'Profile:write']],
    normalizationContext: ['groups' => ['Profile', 'Profile:read']]
)]
class Profile
{
    #[Id]
    #[OneToOne(inversedBy: 'profile', targetEntity: User::class, cascade: ['persist'])]
    #[JoinColumn(onDelete: 'CASCADE')]
    #[ApiProperty(identifier: true, example: '/api/users/84')]
    #[Groups(['Profile:read'])]
    private User $user;

    #[Column]
    #[Groups(['Profile'])]
    private string $name;

    #[Column(nullable: true)]
    #[Groups(['Profile'])]
    private ?string $surname = null;

    #[Column(nullable: true)]
    #[Groups(['Profile'])]
    private ?string $patronymic = null;

    #[Column(length: 32, nullable: true)]
    #[Groups(['Profile'])]
    private ?string $phone = null;

    #[Column(nullable: true)]
    #[Groups(['Profile'])]
    private ?string $email = null;

    #[Column(nullable: true)]
    #[Groups(['Profile'])]
    private ?string $position = null;

    #[Column(type: "text", nullable: true)]
    #[Groups(['Profile'])]
    private ?string $description = null;

    #[OneToOne(targetEntity: Image::class, cascade: ["persist", 'remove'], orphanRemoval: true)]
    #[JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['Profile'])]
    #[ApiProperty(iri: 'http://schema.org/image')]
    private ?Image $image = null;

    #[Pure] public function __construct(User $user, ?string $name = null)
    {
        $this->user = $user;
        $this->name = $name;
        $this->phone = $user->getPhone();
        $this->email = $user->getEmail();
    }

    #[Groups(['Order:read', 'OrderEventLog:read'])]
    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Profile
    {
        $this->user = $user;
        return $this;
    }

    #[Groups(['Order:read', 'OrderEventLog:read', 'Employee:read', 'UserPermissionTemplate:read', 'Contractor:read'])]
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Profile
    {
        $this->name = $name;
        return $this;
    }

    #[Groups(['Order:read', 'OrderEventLog:read', 'Employee:read', 'UserPermissionTemplate:read', 'Contractor:read'])]
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): Profile
    {
        $this->surname = $surname;
        return $this;
    }

    #[Groups(['Order:read', 'OrderEventLog:read', 'Employee:read', 'UserPermissionTemplate:read', 'Contractor:read'])]
    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    public function setPatronymic(?string $patronymic): Profile
    {
        $this->patronymic = $patronymic;
        return $this;
    }

    #[Groups(['Employee:read'])]
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): Profile
    {
        $this->phone = $phone;
        return $this;
    }

    #[Groups(['Employee:read'])]
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): Profile
    {
        $this->email = $email;
        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): Profile
    {
        $this->position = $position;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Profile
    {
        $this->description = $description;
        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    #[Groups(['Order:read', 'OrderEventLog:read'])]
    public function getImageUrl(): ?Image
    {
        return $this->image;
    }
}
