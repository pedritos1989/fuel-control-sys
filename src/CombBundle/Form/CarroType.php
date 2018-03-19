<?php

namespace CombBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarroType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('matricula', TextType::class, array(
                'label' => 'car.code'
            ))
            ->add('modelo', TextType::class, array(
                'label' => 'car.brand'
            ))
            ->add('insptecn', TextType::class, array(
                'label' => 'car.inspection'
            ))
            ->add('indcons', NumberType::class, array(
                'label' => 'car.consumn'
            ))
            ->add('area', EntityType::class, array(
                'label' => 'car.section',
                'class' => 'CombBundle\Entity\Area',
            ))
            ->add('chofer', EntityType::class, array(
                'label' => 'car.driver',
                'class' => 'CombBundle\Entity\Chofer',
            ))
            ->add('estado', EntityType::class, array(
                'label' => 'car.state',
                'class' => 'NomencladorBundle\Entity\EstadoCarro',
            ))
            ->add('tipo', EntityType::class, array(
                'label' => 'car.type',
                'class' => 'NomencladorBundle\Entity\TipoCarro',
            ))
            ->add('tarjeta', EntityType::class, array(
                'label' => 'car.card',
                'class' => 'CombBundle\Entity\Tarjeta',
                'group_by' => 'servicio',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\Carro'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'combbundle_carro';
    }


}
