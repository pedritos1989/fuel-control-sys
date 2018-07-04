<?php

namespace CombBundle\Form;

use CombBundle\Entity\Tarjeta;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChipType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $trans = $options['translator'];
        $builder
            ->add('fecha', DateTimeType::class, array(
                'label' => 'chip.date',
                'widget' => 'single_text',
                'format' => 'd/M/y H:mm',
            ))
            ->add('cantcomb', IntegerType::class, array(
                'label' => 'chip.total',
            ))
            ->add('saldoFinal', IntegerType::class, array(
                'label' => 'chip.finalAmount',
            ))
            ->add('saldoInicial', IntegerType::class, array(
                'label' => 'chip.initialAmount',
            ))
            ->add('tarjeta', EntityType::class, array(
                'label' => 'chip.card',
                'class' => 'CombBundle\Entity\Tarjeta',
                'group_by' => 'servicio',
                'required' => false,
                'choice_attr' => function (Tarjeta $tarjeta, $key, $index) use ($trans) {
                    return [
                        'title' => $trans->trans('card.section') . ': ' . $tarjeta->getArea() . ' - ' . $trans->trans('card.provide') . ': ' . $tarjeta->getAbastecimiento(),
                        'data-initial' => $tarjeta->getSaldoFinal() !== null ? $tarjeta->getSaldoFinal() : 0,
                    ];
                },
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\Chip'
        ));
        $resolver->setRequired('translator');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'combbundle_chip';
    }


}
