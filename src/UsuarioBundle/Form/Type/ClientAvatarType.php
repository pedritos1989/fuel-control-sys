<?php

namespace UsuarioBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientAvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class, array(
            'attr' => array(
                'label' => false,
                'accept' => 'image/jpeg, image/png',
                'required' => false
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UsuarioBundle\Entity\ClientAvatar',
        ));
    }

    public function getBlockPrefix()
    {
        return 'client_avatar_type';
    }
}
