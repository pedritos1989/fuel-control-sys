<?php

namespace CombBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TarjetaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero', TextType::class, array(
                'label' => 'card.number',
            ))
            ->add('lote', IntegerType::class, array(
                'label' => 'card.group',
            ))
            ->add('abastecimiento', IntegerType::class, array(
                'label' => 'card.provide',
            ))
            ->add('fechaVenc', DateType::class, array(
                'label' => 'card.exp.date',
                'widget' => 'single_text',
                'format' => 'd/M/y',
            ))
            ->add('area', EntityType::class, array(
                'class' => 'CombBundle\Entity\Area',
                'required' => false,
                'label' => 'card.section',
            ))
            ->add('servicio', EntityType::class, array(
                'class' => 'NomencladorBundle\Entity\Servicio',
                'required' => false,
                'label' => 'card.service',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\Tarjeta'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'combbundle_tarjeta';
    }


}
