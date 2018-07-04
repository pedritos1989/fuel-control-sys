<?php

namespace CombBundle\Form\EventListener;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class TarjetaFieldSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmitData',
        );
    }

    private function formModifier(FormInterface $form, $area = null)
    {
        $form->add('tarjeta', 'entity', array(
            'class' => 'CombBundle\Entity\Tarjeta',
            'required' => false,
            'label' => 'car.card',
            'query_builder' => function (EntityRepository $er) use ($area) {
                $qb = $er->createQueryBuilder('tarjeta');
                $qb->innerJoin('tarjeta.area', 'area');

                if (is_int($area)) {
                    $qb->where($qb->expr()->eq('area.id', ':area'));
                } else {
                    $qb->where($qb->expr()->eq('area', ':area'));
                }
                $qb->setParameter('area', $area);

                return $qb;
            },
        ));
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $area = null;
        if ($data->getArea() !== null) {
            $area = $data->getArea();
        }

        $this->formModifier($form, $area);
    }

    public function preSubmitData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $area = null;
        if (isset($data['area'])) {
            $area = $data['area'];
        }

        $this->formModifier($form, $area);
    }
}