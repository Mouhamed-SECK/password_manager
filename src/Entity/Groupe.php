<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $privateKey = null;

    #[ORM\OneToMany(mappedBy: 'Groupe', targetEntity: User::class)]
    private  $users;

    #[ORM\OneToOne(mappedBy: 'managedGroup', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private $groupAdmin;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: Password::class)]
    private $passwords;


    public function getId(): ?int
    {
        return $this->id;
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

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->passwords = new ArrayCollection();
    }

    public function __toString() {
        return $this->getTitle();
    }

    
    public function getUsers()
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
     
        
            $this->users->add($user);
            $user->setGroupe($this);
    

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getGroupe() === $this) {
                $user->setGroupe(null);
            }
        }

        return $this;
    }

 
    public function getGroupAdmin(): ?User
    {
        return $this->groupAdmin;
    }


    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }


    public function setPrivateKey(string $privateKey)
    {
        $this->privateKey = $privateKey;
    }



    public function setPrivateKey($privateKey) 
    {
         $this->privateKey = $privateKey;
    }


    public function setGroupAdmin(?User $groupAdmin): self
    {
        // unset the owning side of the relation if necessary
        if ($groupAdmin === null && $this->groupAdmin !== null) {
            $this->groupAdmin->setManagedGroup(null);
        }

        // set the owning side of the relation if necessary
        if ($groupAdmin !== null && $groupAdmin->getManagedGroup() !== $this) {
            $groupAdmin->setManagedGroup($this);
        }

        $this->groupAdmin = $groupAdmin;

        return $this;
    }

    /**
     * @return Collection<int, Password>
     */
    public function getPasswords(): Collection
    {
        return $this->passwords;
    }

    public function addPassword(Password $password): self
    {
        if (!$this->passwords->contains($password)) {
            $this->passwords[] = $password;
            $password->setGroupe($this);
        }

        return $this;
    }

    public function removePassword(Password $password): self
    {
        if ($this->passwords->removeElement($password)) {
            // set the owning side to null (unless already changed)
            if ($password->getGroupe() === $this) {
                $password->setGroupe(null);
            }
        }

        return $this;
    }
}
