<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use App\Entity\Booking;

class CommentRequiredIfRefusedValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        // Assurez-vous que la contrainte est bien celle attendue
        if (!$constraint instanceof CommentRequiredIfRefused) {
            throw new UnexpectedTypeException($constraint, CommentRequiredIfRefused::class);
        }

        // Récupérez l'entité actuellement validée
        $booking = $this->context->getObject();

        // Vérifiez si le statut est "Refusée" et le champ de commentaire est vide
        if ($booking->getStatusBooking() === Booking::STATUS_DECLINED && empty($booking->getCommentsHote())) {
            $this->context->buildViolation($constraint->message)
                ->atPath('comments_hote') // Ciblez le champ de commentaire pour l'erreur
                ->addViolation();
        }
    }
}