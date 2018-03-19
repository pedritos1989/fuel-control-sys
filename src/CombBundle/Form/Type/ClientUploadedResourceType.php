<?php

namespace CombBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientUploadedResourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('attachmentName', TextType::class, array(
                'label' => 'client.uploadresource.attachmentName',
            ))
            ->add('file', FileType::class, array(
                'attr' => array(
                    'accept' => 'text/plain, application/pdf, application/msword, image/gif, image/jpeg, image/png,
                    application/vnd.openxmlformats-officedocument.wordprocessingml.document, 
                    application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/zip, 
                    application/x-compressed-zip, application/x-rar-compressed, application/wps-office.xlsx',
                ),
                'label' => 'client.uploadresource.label',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\ClientUploadedResource',
        ));
    }

    public function getBlockPrefix()
    {
        return 'client_uploadedresource_type';
    }
}
