<?php

namespace Orchestra\GeneratorBundle\Command;

use Orchestra\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Orchestra\GeneratorBundle\Generator\DoctrineFormGenerator;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand as BaseClass;

class GenerateDoctrineCrudCommand extends BaseClass
{
    /**
     * {@inheritdoc}
     */
    protected function createGenerator($bundle = null)
    {
        return new DoctrineCrudGenerator(
            $this->getContainer()->get('filesystem'),
            $this->getContainer()->getParameter('kernel.root_dir')
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormGenerator($bundle = null)
    {
        $formGenerator = new DoctrineFormGenerator($this->getContainer()->get('filesystem'));
        $formGenerator->setSkeletonDirs($this->getSkeletonDirs($bundle));

        return $formGenerator;
    }
}
