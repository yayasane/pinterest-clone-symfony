<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $firstNameConstraint = [
            new NotBlank(['message' => 'Please enter your first name']),
        ];
        $lastNameConstraint = [
            new NotBlank(['message' => 'Please enter your last name']),
        ];

        $emailConstraints = [
            new NotBlank(['message' => 'Please enter your email adress']),
            new Email(['message' => 'Please enter a valid email adress'])
        ];

        $builder
            ->add('firstName', TextType::class, [
                'constraints' => $firstNameConstraint,
            ])
            ->add('lastName', TextType::class, [
                'constraints' => $firstNameConstraint,
            ])
            ->add('email', EmailType::class, [
                'constraints' => $emailConstraints,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
