<?php

namespace CombBundle\Form;

use CombBundle\Form\EventListener\TarjetaFieldSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('marca', EntityType::class, array(
                'label' => 'car.brand',
                'class' => 'NomencladorBundle\Entity\Marca',
            ))
            ->add('insptecn', DateType::class, array(
                'label' => 'car.inspection',
                'widget' => 'single_text',
                'format' => 'd/M/y',
            ))
            ->add('indcons', NumberType::class, array(
                'label' => 'car.consumn',
            ))
            ->add('area', EntityType::class, array(
                'label' => 'car.section',
                'class' => 'CombBundle\Entity\Area',
                'required' => false,
            ))
            ->add('chofer', EntityType::class, array(
                'label' => 'car.driver',
                'class' => 'CombBundle\Entity\Chofer',
                'required' => false,
            ))
            ->add('estado', EntityType::class, array(
                'label' => 'car.state',
                'class' => 'NomencladorBundle\Entity\EstadoCarro',
            ))
            ->add('tipo', EntityType::class, array(
                'label' => 'car.type',
                'class' => 'NomencladorBundle\Entity\TipoCarro',
            ));
        $builder->addEventSubscriber(new TarjetaFieldSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\Carro',
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
