<?php


namespace App\Security\Voter;


use App\Entity\Etat;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SortieVoter extends Voter
{
    const SORTIE_EDIT = 'sortie_edit';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::SORTIE_EDIT])
            && $subject instanceof \App\Entity\Sortie;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $sortie = $subject;
        $utilisateur = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => $user->getUserIdentifier()]);

        if ($utilisateur == $sortie->getCreateur()) {
            if ($sortie->getEtat() == Etat::ETAT_OUVERT || $sortie->getEtat() == Etat::ETAT_EN_CREATION) {

                return true;
            }
        }

        return false;
    }
}