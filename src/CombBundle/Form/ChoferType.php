<?php

namespace CombBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoferType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ci', TextType::class, array(
                'label' => 'driver.identity',
                'max_length' => 11,
            ))
            ->add('nombre', TextType::class, array(
                'label' => 'driver.name',
            ))
            ->add('apellido', TextType::class, array(
                'label' => 'driver.surname',
            ))
            ->add('licencia', TextType::class, array(
                'label' => 'driver.drivPerm',
                'max_length' => 7,
            ))
            ->add('telefono', IntegerType::class, array(
                'label' => 'driver.phone',
                'required' => false,
            ))
            ->add('direccion', TextareaType::class, array(
                'label' => 'driver.address',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\Chofer'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'combbundle_chofer';
    }


}
