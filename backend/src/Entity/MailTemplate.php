<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\MailTemplateRepository;
use App\State\MailSetOwnerProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MailTemplateRepository::class)]
#[ApiResource]
#[Delete(security: "object.user == user")]
#[Post(
    uriTemplate: "/mails",
    security: "is_granted('ROLE_USER')",
    processor: MailSetOwnerProcessor::class)]
#[Patch(
    uriTemplate: "/mails/{id}",
    security: "object.user == user",
)]
class MailTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['item:mail', 'collection:mail'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['item:mail', 'collection:mail'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['item:mail', 'collection:mail'])]
    private ?string $behaviour = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['item:mail', 'collection:mail'])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'mails')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['item:mail'])]
    public ?User $user = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBehaviour(): ?string
    {
        return $this->behaviour;
    }

    public function setBehaviour(string $behaviour): static
    {
        $this->behaviour = $behaviour;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }


}
