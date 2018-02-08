<?php

namespace CombBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DistribucionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plan', IntegerType::class, array(
                'label' => 'distribution.plan',
            ))
            ->add('asignacion', IntegerType::class, array(
                'label' => 'distribution.asignation',
            ))
            ->add('solicitudes', null, array(
                'label' => 'distribution.request',
                'class' => 'CombBundle\Entity\Solicitud',
                'multiple' => 1,
            ))
            ->add('planAsignacion', EntityType::class, array(
                'label' => 'distribution.asignPlan',
                'class' => 'CombBundle\Entity\PlanAsignacion',
            ))
            ->add('tarjetas', null, array(
                'label' => 'distribution.card',
                'class' => 'CombBundle\Entity\Tarjeta',
                'multiple' => 1,
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\Distribucion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'combbundle_distribucion';
    }


}
