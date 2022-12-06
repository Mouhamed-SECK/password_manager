<?php

namespace App\Entity;

use App\Repository\PasswordLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PasswordLogRepository::class)]
class PasswordLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Password::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $concernPassword;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $concernUser;

    #[ORM\Column(type: 'time')]
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConcernPassword(): ?Password
    {
        return $this->concernPassword;
    }

    public function setConcernPassword(?Password $concernPassword): self
    {
        $this->concernPassword = $concernPassword;

        return $this;
    }

    public function getConcernUser(): ?User
    {
        return $this->concernUser;
    }

    public function setConcernUser(?User $concernUser): self
    {
        $this->concernUser = $concernUser;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
