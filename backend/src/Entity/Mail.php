<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\MailRepository;
use App\Repository\MailTemplateRepository;
use App\State\MailSetOwnerProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MailRepository::class)]
#[ApiResource]
#[Delete(uriTemplate: "/mail/{id}", security: "object.user == user")]
#[Get(uriTemplate: "/mail/{id}" , security: "object.user == user")]
#[Post(
    uriTemplate: "/mails",
    security: "is_granted('ROLE_USER')",
    processor: MailSetOwnerProcessor::class)]
#[Patch(
    uriTemplate: "/mails/{id}",
    security: "object.user == user",
)]
class Mail
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

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["item:mail", 'collection:mail'])]
    private ?string $token = null;

    #[ORM\Column(length: 255)]
    #[Groups(["item:mail", 'collection:mail'])]
    private ?string $subject = null;

    #[ORM\Column]
    #[Groups(["item:mail", 'collection:mail'])]
    private ?bool $is_deploy = null;

    #[ORM\Column]
    private ?bool $is_template = null;

    /**
     * @param string|null $token
     */
    public function __construct()
    {
        $this->token = Uuid::v4();
        $this->is_deploy = false;
        $this->is_template = false;
    }


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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function isDeploy(): ?bool
    {
        return $this->is_deploy;
    }

    public function setDeploy(bool $is_deploy): static
    {
        $this->is_deploy = $is_deploy;

        return $this;
    }

    public function isTemplate(): ?bool
    {
        return $this->is_template;
    }

    public function setTemplate(bool $is_template): static
    {
        $this->is_template = $is_template;

        return $this;
    }


}
