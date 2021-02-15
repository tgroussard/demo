<?php

namespace App\Form;

use App\Entity\Outing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('startDate', DateTimeType::class)
            ->add('duration', NumberType::class)
            ->add('endRegisterDate', DateType::class)
            ->add('maxRegister', NumberType::class)
            ->add('description', TextareaType::class)
            ->add('place', ChoiceType::class, ['choices' => $options['places']])
            ->add('organizerSite', ChoiceType::class, ['choices' => $options['campus']])
            ->add('city', ChoiceType::class, ['mapped' => false, 'choices' => $options['cities']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
            'campus' => [],
            'places' => [],
            'cities' => []
        ]);
    }
}
