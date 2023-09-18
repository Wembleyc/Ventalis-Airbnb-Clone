<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CreateAdminVoter extends Voter
{
    public const CREATE_ADMIN = 'create_admin';

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::CREATE_ADMIN;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Only allow admin to create another admin.
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}
