<?php

namespace Orchestra\GeneratorBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator as BaseClass;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class DoctrineCrudGenerator extends BaseClass
{
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite)
    {
        parent::generate($bundle, $entity, $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite);

        $dir = sprintf('%s/Resources/views/%s', $this->rootDir, strtolower($entity));

        if (in_array('delete', $this->actions)) {
//            $this->generateDeleteModalView($dir);
        }

        $this->generateFormView($dir);
        $this->generateListView($dir);
//        $this->generateFormFilterView($dir);
    }

    protected function generateFormView($dir)
    {
        $this->renderFile('crud/views/_form.html.twig.twig', $dir.'/_form.html.twig', array(
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'entity_singularized' => $this->entitySingularized,
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'fields' => $this->metadata->fieldMappings,
            'identifier' => $this->metadata->identifier[0],
        ));
    }

    protected function generateListView($dir)
    {
        $this->renderFile('crud/views/_list.html.twig.twig', $dir.'/_list.html.twig', array(
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'entity_singularized' => $this->entitySingularized,
            'entity_pluralized' => $this->entityPluralized,
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'fields' => $this->metadata->fieldMappings,
            'record_actions' => $this->getRecordActions(),
            'identifier' => $this->metadata->identifier[0],
        ));
    }

    protected function generateFormFilterView($dir)
    {
        $this->renderFile('crud/views/_form_filter.html.twig.twig', $dir.'/_form_filter.html.twig', array(
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'entity_singularized' => $this->entitySingularized,
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'fields' => $this->metadata->fieldMappings,
            'identifier' => $this->metadata->identifier[0],
        ));
    }

    protected function generateDeleteModalView($dir)
    {
        $this->renderFile('crud/views/_delete_modal.html.twig.twig', $dir.'/_delete_modal.html.twig', array(
            'entity' => $this->entity,
            'entity_singularized' => $this->entitySingularized,
            'identifier' => $this->metadata->identifier[0],
        ));
    }

    /**
     * Generates the controller class only.
     */
    protected function generateControllerClass($forceOverwrite)
    {
        $dir = $this->bundle->getPath();

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/Controller/%s/%sController.php',
            $dir,
            str_replace('\\', '/', $entityNamespace),
            $entityClass
        );

        if (!$forceOverwrite && file_exists($target)) {
            throw new \RuntimeException('Unable to generate the controller as it already exists.');
        }

        $this->renderFile('crud/controller.php.twig', $target, array(
            'actions' => $this->actions,
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'entity_singularized' => $this->entitySingularized,
            'entity_pluralized' => $this->entityPluralized,
            'entity_class' => $entityClass,
            'namespace' => $this->bundle->getNamespace(),
            'entity_namespace' => $entityNamespace,
            'format' => $this->format,
            'fields' => $this->metadata->fieldMappings,
            // BC with Symfony 2.7
            'use_form_type_instance' => !method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix'),
        ));
    }

    /**
     * Returns an array of record actions to generate (edit, show).
     *
     * @return array
     */
    protected function getRecordActions()
    {
        return array_filter($this->actions, function ($item) {
            return in_array($item, array('show', 'edit', 'delete'));
        });
    }
}
