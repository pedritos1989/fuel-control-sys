<?php

namespace Dundivet\UploadBundle\Doctrine;

use Dundivet\UploadBundle\Interfaces\UploadAwareInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class UploadEventsSubscriber implements EventSubscriber
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        if ($object instanceof UploadAwareInterface) {
            $object->preUpload();
            $object->upload();
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        if ($object instanceof UploadAwareInterface) {
            $object->preUpload();
            $object->upload();
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        if ($object instanceof UploadAwareInterface) {
            $object->removeUpload();
        }
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
        );
    }
}
