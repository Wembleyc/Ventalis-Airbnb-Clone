<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Attribute; // Ajoutez cet import

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"}) // Cela signifie que la contrainte peut être utilisée sur une propriété, une méthode ou comme une autre annotation.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)] // Cela permet d'utiliser la contrainte comme un attribut.
class CommentRequiredIfRefused extends Constraint
{
    public $message = 'Un commentaire est requis lorsque le statut est refusé.';
}