<?php

namespace CombBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanAsignacionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', DateType::class, array(
                'label' => 'assign.plan.date',
                'widget' => 'single_text',
                'format' => 'd/M/y',
            ))
            ->add('cantcomb', IntegerType::class, array(
                'label' => 'assign.plan.total',
            ))
            ->add('servicio', EntityType::class, array(
                'class' => 'NomencladorBundle\Entity\Servicio',
                'required' => false,
                'label' => 'assign.plan.service',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\PlanAsignacion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'combbundle_planasignacion';
    }


}
