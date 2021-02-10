<?php

namespace App\Form;

use App\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $types = [];
        foreach (Item::TYPES as $key => $type) {
            $types[ucfirst($type)] = $type;
        }

        $builder
            ->add('name', TextType::class)
            ->add('type', ChoiceType::class, [
                'choices' => $types,
                'placeholder' => 'Select type',
                'required' => true
            ])
            ->add('description', TextType::class)
            ->add('price', NumberType::class, [
                'scale' => 2
            ])
            ->add('save', SubmitType::class)
        ;
    }
}