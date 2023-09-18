<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Entity\Booking;

class MinimumOneGuestValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint App\Validator\Constraints\MinimumOneGuest */

        if (null === $value || '' === $value) {
            return;
        }

        if ($value < 1) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
