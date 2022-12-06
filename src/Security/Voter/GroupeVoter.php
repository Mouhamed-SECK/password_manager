<?php

namespace App\Security\Voter;

use App\Entity\Groupe;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupeVoter extends Voter
{
    const EDIT = "GROUPE_EDIT";
    const DELETE = "GROUPE_DELETE";
    const CREATE = "GROUPE_CREATE";

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $groupe): bool
    {
        if (!in_array($attribute, [self::CREATE, self::DELETE])) {
            return false;
        }
        if (!$groupe instanceof Groupe) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(
        $attribute,
        $groupe,
        TokenInterface $token
    ): bool {
        // On récupère l'utilisateur à partir du token
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // On vérifie si l'utilisateur est admin
        if ($this->security->isGranted("ROLE_ADMIN")) {
            return true;
        }

        // On vérifie les permissions
        switch ($attribute) {
            case self::EDIT:
                // On vérifie si l'utilisateur peut éditer
                return $this->isSuperAdmin();
                break;
            case self::CREATE:
                // On vérifie si l'utilisateur peut supprimer
                return $this->isSuperAdmin();
                break;
        }
    }

    private function isSuperAdmin()
    {
        return $this->security->isGranted("ROLE_SUPER_ADMIN");
    }
}
