<?php

namespace CombBundle\Form\EventListener;

use CombBundle\Entity\DistribucionXTarjeta;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class DistXTarjFieldSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmitData',
        );
    }

    private function formModifier(FormInterface $form, $tarjeta = null)
    {
        $firstDay = date('Y') . '-' . date('m') . '-' . '1';
        $lastDay = date('Y') . '-' . date('m') . '-' . date('t');
        $form->add('distTrjt', EntityType::class, array(
            'class' => DistribucionXTarjeta::class,
            'required' => false,
            'label' => 'request.card.dist.card',
            'query_builder' => function (EntityRepository $er) use ($tarjeta, $firstDay, $lastDay) {
                $qb = $er->createQueryBuilder('dist');
                $qb->leftJoin('dist.tarjeta', 'tarjeta')
                    ->leftJoin('dist.distribucion', 'distribucion');
                if (\is_int($tarjeta)) {
                    $qb->andWhere($qb->expr()->eq('tarjeta.id', ':tarjeta'));
                } else {
                    $qb->andWhere($qb->expr()->eq('tarjeta', ':tarjeta'));
                }
                $qb->andWhere($qb->expr()->gte('distribucion.fecha', ':firstDay'))
                    ->setParameter('firstDay', $firstDay);

                $qb->andWhere($qb->expr()->lte('distribucion.fecha', ':lastDay'))
                    ->setParameter('lastDay', $lastDay);

                $qb->setParameter('tarjeta', $tarjeta);

                return $qb;
            },
        ));
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $tarjeta = null;
        if ($data->getTarjeta() !== null) {
            $tarjeta = $data->getTarjeta();
        }

        $this->formModifier($form, $tarjeta);
    }

    public function preSubmitData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $tarjeta = null;
        if (isset($data['tarjeta'])) {
            $tarjeta = $data['tarjeta'];
        }

        $this->formModifier($form, $tarjeta);
    }
}