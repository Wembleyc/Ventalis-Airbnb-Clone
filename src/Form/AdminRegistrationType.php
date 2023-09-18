<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class AdminRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('telephone')
            ->add('email')
            ->add('password') // Ajoutez le champ pour le mot de passe de l'administrateur
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Hôte' => 'ROLE_HOST',
                    'Utilisateur' => 'ROLE_USER',
                    'Employé' => 'ROLE_EMPLOYEE',
                ],
                'multiple' => true,
                'expanded' => true,
            ]);

        // Add the CallbackTransformer to convert roles between form and entity
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesAsArray) {
                    return count($rolesAsArray) ? $rolesAsArray[0] : null;
                },
                function ($rolesAsString) {
                    return [$rolesAsString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}