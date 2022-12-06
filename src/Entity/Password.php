<?php

namespace App\Entity;

use App\Repository\PasswordRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PasswordRepository::class)]
class Password
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $description;

    #[ORM\Column(type: 'string', length: 255)]
    private $url;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image;

    #[ORM\Column(type: 'string', length: 255)]
    private $encryptedPassword;

    #[ORM\ManyToOne(targetEntity: Groupe::class, inversedBy: 'passwords')]
    #[ORM\JoinColumn(nullable: false)]
    private $groupe;

    #[ORM\Column(type: 'datetime' )]
    private $createdAt;

    #[ORM\Column(type: 'string', length: 255)]
    private $usedLogin;

    #[ORM\Column(type: 'string', length: 255)]
    private $recuparationEmail;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
  
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getEncryptedPassword(): ?string
    {
        return $this->encryptedPassword;
    }

    public function setEncryptedPassword(string $encryptedPassword): self
    {
        $this->encryptedPassword = $encryptedPassword;

        return $this;
    }

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUsedLogin(): ?string
    {
        return $this->usedLogin;
    }

    public function setUsedLogin(string $usedLogin): self
    {
        $this->usedLogin = $usedLogin;

        return $this;
    }

    public function getRecuparationEmail(): ?string
    {
        return $this->recuparationEmail;
    }

    public function setRecuparationEmail(string $recuparationEmail): self
    {
        $this->recuparationEmail = $recuparationEmail;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
