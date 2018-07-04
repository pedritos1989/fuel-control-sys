<?php

namespace UsuarioBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserRegistrationType
 * @package UsuarioBundle\Form\Type
 */
class UserRegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', 'checkbox', array(
                'label' => 'user.active',
                'required' => false,
            ))
            ->add('groups', 'entity', array(
                'label' => 'user.rol.label',
                'class' => 'UsuarioBundle\Entity\Group',
                'multiple' => true,
            ))
            ->add('avatar', ClientAvatarType::class, array(
                'required' => false,
                'label' => 'client.avatar',
                'attr' => array(
                    'class' => 'hidden',
                ),
            ))
            ->add('persona', 'entity', array(
                'label' => 'nomenclator.people.label',
                'required' => false,
                'class' => 'NomencladorBundle\Entity\Persona',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UsuarioBundle\Entity\User',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'registration_user_type';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return $this->getName();
    }
}
