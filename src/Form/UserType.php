<?php

namespace App\Form;

use App\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $roles = [
            'User' => 'ROLE_USER',
            'Admin' => 'ROLE_ADMIN',
            'Cook' => 'ROLE_COOK',
            'Waiter' => 'ROLE_WAITER'
        ];

        $builder
            ->add('username', TextType::class)
            ->add('roles', ChoiceType::class, [
                'choices' => $roles,
                'placeholder' => 'Select role',
                'multiple' => true,
                'required' => true
            ])
            ->add('password', PasswordType::class)
            ->add('save', SubmitType::class)
        ;
    }
}