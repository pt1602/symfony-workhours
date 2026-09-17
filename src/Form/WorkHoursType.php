<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkHoursType extends AbstractType
{
    private const DAYS = [
        'Montag'     => 'monday',
        'Dienstag'   => 'tuesday',
        'Mittwoch'   => 'wednesday',
        'Donnerstag' => 'thursday',
        'Freitag'    => 'friday',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach (self::DAYS as $label => $key) {
            $builder->add($key, NumberType::class, [
                'label'    => $label,
                'required' => false,
                'html5'    => true,
                'scale'    => 1,
                'attr'     => ['min' => 0, 'max' => 12, 'step' => 0.25],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'empty_data' => [],
        ]);
    }
}
