<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class CreateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('firstName', null, [
            'label' => 'Prénom: ',
        ])
        ->add('lastName', null, [
            'label' => 'Nom: ',
        ])
        ->add('email', EmailType::class, [
            'label' => 'Adresse Email: ',
        ])
        ->add('telephone', null, [
            'label' => 'Téléphone: ',
        ])
        ->add('plainPassword', PasswordType::class, [
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    'max' => 4096,
                ]),
            ],
            'label' => 'Mot de passe: ',
        ])
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Admin' => 'ROLE_ADMIN',
                'User' => 'ROLE_USER',
                'Hote' => 'ROLE_HOTE',
                'Employe' => 'ROLE_EMPLOYE',
            ],
            'multiple' => true,
            'expanded' => false,
            'required' => true,
            'attr' => [
                'class' => 'form-control',
            ],
            'label' => 'Rôles',
        ])
        ->add('employee_matricule', NumberType::class, [
            'label' => 'Matricule: ',
            'required' => false,
        ]);    
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
