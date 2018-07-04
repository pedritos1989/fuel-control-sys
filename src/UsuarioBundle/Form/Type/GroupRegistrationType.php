<?php

namespace UsuarioBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GroupRegistrationType
 * @package UsuarioBundle\Form\Type
 */
class GroupRegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', 'choice', array(
                'multiple' => true,
                'choices' => array(
                    'ROLE_DIRECTOR' => 'user.rol.role_director',
                    'ROLE_JEFE' => 'user.rol.role_jefe',
                    'ROLE_TECNICO' => 'user.rol.role_tecnico',
                    'ROLE_CAJERO' => 'user.rol.role_cajero',
                ),
                'label' => 'user.rol.label',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UsuarioBundle\Entity\Group',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'registration_group_type';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\GroupFormType';
    }

    public function getBlockPrefix()
    {
        return $this->getName();
    }
}