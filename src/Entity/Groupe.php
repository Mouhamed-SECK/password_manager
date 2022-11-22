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
    private Collection $users;

    #[ORM\OneToOne(mappedBy: 'managedGroup', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private $groupAdmin;



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
    }

    public function __toString() {
        return $this->getTitle();
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setGroupe($this);
        }

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
}
