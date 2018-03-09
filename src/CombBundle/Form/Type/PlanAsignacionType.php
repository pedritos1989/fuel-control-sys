<?php

namespace CombBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('area', EntityType::class, array(
                'class' => 'CombBundle\Entity\Area',
                'required' => false,
                'label' => 'assign.plan.section',
            ))
            ->add('asignacionMensual', EntityType::class, array(
                'label' => 'assign.plan.monthly.asign',
                'class' => 'CombBundle\Entity\AsignacionMensual',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\PlanAsignacion',
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
