<?php

namespace App\Form;

use App\Entity\Week;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CreateWeekFromType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('calenderWeek', null, [
                'label' => 'Kalenderwoche',
            ])
            ->add('year', null, [
                'label' => 'Jahr',
            ])
            ->add('workHours', WorkHoursType::class, [
                'label' => 'Arbeitsstunden pro Wochentag',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Speichern',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Week::class,
        ]);
    }
}
