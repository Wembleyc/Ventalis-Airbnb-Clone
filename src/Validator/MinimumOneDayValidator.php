<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Entity\Booking;

class MinimumOneDayValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $booking Booking */
        $booking = $this->context->getObject();

        if (!$booking instanceof Booking) {
            return;
        }

        $startDate = $booking->getStartDate();
        $endDate = $booking->getEndDate();

        if (null === $endDate || null === $startDate) {
            return;
        }

        $interval = $startDate->diff($endDate);

        if ($interval->days < 1 || $interval->invert)
 {
            $this->context->buildViolation($constraint->message)
                ->atPath('end_date') // This will point the violation to the end_date field
                ->addViolation();
        }
    }

}
