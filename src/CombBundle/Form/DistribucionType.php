<?php

namespace CombBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DistribucionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('planAsignacion', EntityType::class, array(
                'label' => 'distribution.plan',
                'class' => 'CombBundle\Entity\PlanAsignacion',
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('p');
                    $qb->andWhere($qb->expr()->gte('p.fecha', ':firstDay'))
                        ->andWhere($qb->expr()->lte('p.fecha', ':lastDay'));
                    $qb->setParameter('firstDay', date('Y') . '-' . date('m') . '-' . '1')
                        ->setParameter('lastDay', date('Y') . '-' . date('m') . '-' . date('t'));
                    return $qb;
                },
                'group_by' => 'asignacionMensual',
            ))
            ->add('fecha', DateTimeType::class, array(
                'label' => 'distribution.date',
                'widget' => 'single_text',
                'format' => 'd/M/y',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\Distribucion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'combbundle_distribucion';
    }


}
