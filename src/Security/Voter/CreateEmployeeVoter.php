<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CreateEmployeeVoter extends Voter
{
    public const CREATE_EMPLOYEE = 'create_employee';

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::CREATE_EMPLOYEE;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Allow both admin and host to create employee.
        return in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_HOST', $user->getRoles());
    }
}
