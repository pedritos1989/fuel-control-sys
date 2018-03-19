<?php

namespace CombBundle\Form\Type;

use CombBundle\Entity\Tarjeta;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DistribucionXTarjetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $trans = $options['translator'];
        $data = $options['data'];
        $builder
            ->add('tarjeta', EntityType::class, array(
                'label' => 'card.dist.card',
                'class' => 'CombBundle\Entity\Tarjeta',
                'query_builder' => function (EntityRepository $er) use ($data) {
                    $qb = $er->createQueryBuilder('t');
                    $qb->andWhere($qb->expr()->eq('t.area', ':paramArea'));
                    $qb->setParameter('paramArea', $data->getDistribucion()->getPlanAsignacion()->getArea()->getId());
                    return $qb;
                },
                'required' => false,
                'group_by' => 'servicio',
                'choice_attr' => function (Tarjeta $tarjeta, $key, $index) use ($trans) {
                    return [
                        'title' => $trans->trans('card.section') . ': ' . $tarjeta->getArea() . ' - ' . $trans->trans('card.provide') . ': ' . $tarjeta->getAbastecimiento()
                    ];
                },
            ))
            ->add('asignacion', IntegerType::class, array(
                'label' => 'card.dist.value',
            ))
            ->add('solicitudes', null, array(
                'label' => 'card.dist.requests',
                'class' => 'CombBundle\Entity\Solicitud',
                'multiple' => true,
                'query_builder' => function (EntityRepository $er2) use ($data) {
                    $qb2 = $er2->createQueryBuilder('s');
                    $qb2->andWhere($qb2->expr()->eq('s.area', ':paramArea'));
                    $qb2->setParameter('paramArea', $data->getDistribucion()->getPlanAsignacion()->getArea()->getId());
                    return $qb2;
                }
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CombBundle\Entity\DistribucionXTarjeta',
        ));
        $resolver->setRequired('translator');
    }

    public function getBlockPrefix()
    {
        return 'dist_x_tarj_type';
    }
}
