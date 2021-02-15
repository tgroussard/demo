<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', ChoiceType::class, ['empty_data' => $options['defaultCampus'], 'choices' => $options['campus']])
            ->add('name', TextType::class, ['required' => false])
            ->add('dateStart', DateType::class, ['required' => false])
            ->add('dateEnd', DateType::class, ['required' => false])
            ->add('checkOrganizer', CheckboxType::class, ['required' => false, 'data' => true])
            ->add('checkRegister', CheckboxType::class, ['required' => false, 'data' => true])
            ->add('checkNotRegister', CheckboxType::class, ['required' => false, 'data' => true])
            ->add('checkPast', CheckboxType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'campus' => [],
            'defaultCampus' => ''
        ]);
    }
}
