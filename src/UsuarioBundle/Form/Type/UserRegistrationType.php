<?php

namespace UsuarioBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType.
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
            ->add('roles', 'choice', array(
                'choices' => array(
                    'DIRECTOR_TRANSPORTE' => 'Director(a) de Transporte',
                    'JEFE_AREA' => 'Jefe(a) de Área',
                    'TECNICO_TRANSPORTE' => 'Técnico(a) de Transporte',
                    'CAJERO' => 'Cajero(a)',
                ),
                'multiple' => true,
                'label' => 'user.rol.label',
                'attr' => array(
                    'style' => 'width:100%',
                ),
            ))
            ->add('avatar', ClientAvatarType::class, array(
                'required' => false,
                'label' => 'client.avatar',
                'attr' => array(
                    'class' => 'hidden',
                ),
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
