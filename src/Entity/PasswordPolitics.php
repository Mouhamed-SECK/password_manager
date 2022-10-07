<?php

namespace App\Entity;

use App\Repository\PasswordPoliticsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PasswordPoliticsRepository::class)]
class PasswordPolitics
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nbrOfChar = null;

    #[ORM\Column]
    private ?bool $HasLowerCaseLetter = null;

    #[ORM\Column]
    private ?bool $HasCapitalLetter = null;

    #[ORM\Column]
    private ?bool $HasSpecialLetter = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbrOfChar(): ?int
    {
        return $this->nbrOfChar;
    }

    public function setNbrOfChar(int $nbrOfChar): self
    {
        $this->nbrOfChar = $nbrOfChar;

        return $this;
    }

    public function HasLowerCaseLetter(): ?bool
    {
        return $this->HasLowerCaseLetter;
    }

    public function setHasLowerCaseLetter(bool $HasLowerCaseLetter): self
    {
        $this->HasLowerCaseLetter = $HasLowerCaseLetter;

        return $this;
    }

    public function HasCapitalLetter(): ?bool
    {
        return $this->HasCapitalLetter;
    }

    public function setHasCapitalLetter(bool $HasCapitalLetter): self
    {
        $this->HasCapitalLetter = $HasCapitalLetter;

        return $this;
    }

    public function HasSpecialLetter(): ?bool
    {
        return $this->HasSpecialLetter;
    }

    public function setHasSpecialLetter(bool $HasSpecialLetter): self
    {
        $this->HasSpecialLetter = $HasSpecialLetter;

        return $this;
    }
}
