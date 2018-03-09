<?php

namespace CombBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AsignacionXServicioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('servicio', EntityType::class, array(
                'label' => 'service.assign.service',
                'class' => 'NomencladorBundle\Entity\Servicio',
            ))
            ->add('cantidad', NumberType::class, array(
                'label' => 'service.assign.amount',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\AsignacionXServicio',
        ));
    }

    public function getBlockPrefix()
    {
        return 'asign_x_servicio_type';
    }
}
