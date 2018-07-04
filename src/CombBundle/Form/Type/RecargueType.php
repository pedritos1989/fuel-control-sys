<?php

namespace CombBundle\Form\Type;

use CombBundle\Entity\Recargue;
use CombBundle\Entity\Tarjeta;
use CombBundle\Form\EventListener\DistXTarjFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecargueType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $trans = $options['translator'];
        $builder
            ->add('tarjeta', EntityType::class, array(
                'class' => Tarjeta::class,
                'required' => false,
                'label' => 'request.card.card',
                'group_by' => 'servicio',
                'choice_attr' => function (Tarjeta $tarjeta, $key, $index) use ($trans) {
                    return [
                        'title' => $trans->trans('card.section') . ': ' . $tarjeta->getArea()
                    ];
                },
            ))
            ->add('fecha', DateTimeType::class, array(
                'label' => 'request.card.date',
                'widget' => 'single_text',
                'format' => 'd/M/y H:mm',
            ))
            ->add('responsable', TextType::class, array(
                'label' => 'request.card.manager'
            ));
        $builder->addEventSubscriber(new DistXTarjFieldSubscriber());
    }

    /**
     * @param OptionsResolver $resolver
     * {@inheritdoc}
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Recargue::class
        ));
        $resolver->setRequired('translator');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'combbundle_recargue';
    }


}
