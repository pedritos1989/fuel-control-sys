<?php

namespace CombBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChipType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', DateTimeType::class, array(
                'label' => 'chip.date',
                'widget' => 'single_text',
                'format' => 'd/M/y H:mm',
            ))
            ->add('cantcomb', NumberType::class, array(
                'label' => 'chip.total',
            ))
            ->add('saldoInicial', NumberType::class, array(
                'label' => 'chip.initialAmount',
            ))
            ->add('saldoFinal', NumberType::class, array(
                'label' => 'chip.finalAmount',
            ))
            ->add('tarjeta', EntityType::class, array(
                'label' => 'chip.card',
                'class' => 'CombBundle\Entity\Tarjeta',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\Chip'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'combbundle_chip';
    }


}
