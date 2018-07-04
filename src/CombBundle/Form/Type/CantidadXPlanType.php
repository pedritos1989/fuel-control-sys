<?php

namespace CombBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CantidadXPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('servicio', EntityType::class, array(
                'label' => 'service.assign.service',
                'class' => 'NomencladorBundle\Entity\Servicio',
                'required' => false,
            ))
            ->add('cantidad', IntegerType::class, array(
                'label' => 'service.assign.amount',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\CantidadXPlan',
        ));
    }

    public function getBlockPrefix()
    {
        return 'cant_x_plan_type';
    }
}
