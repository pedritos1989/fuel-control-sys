<?php

namespace CombBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SolicitudType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechaHoraS', DateTimeType::class, array(
                'label' => 'request.date.out',
                'widget' => 'single_text',
                'format' => 'd/M/y H:mm',
            ))
            ->add('fechaHoraR', DateTimeType::class, array(
                'label' => 'request.date.in',
                'widget' => 'single_text',
                'format' => 'd/M/y H:mm',
            ))
            ->add('lugar', TextType::class, array(
                'label' => 'request.place',
            ))
            ->add('motivo', TextareaType::class, array(
                'label' => 'request.reason',
            ))
            ->add('cantpersona', IntegerType::class, array(
                'label' => 'request.people.count',
            ))
            ->add('area', EntityType::class, array(
                'class' => 'CombBundle\Entity\Area',
                'required' => false,
                'label' => 'request.section',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\Solicitud'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'combbundle_solicitud';
    }


}
