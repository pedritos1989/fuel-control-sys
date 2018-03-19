<?php

namespace CombBundle\Manager;

use AppBundle\Manager\AbstractManager;
use CombBundle\Entity\Area;
use Doctrine\DBAL\Types\Type;

/**
 * Class AreaAchildsManager
 * @package CombBundle\Manager
 */
class AreaChildsManager extends AbstractManager
{
    /**
     * @param Area $area
     */
    public function applyFilterByArea(Area $area)
    {
        $filter = $this->em
            ->getFilters()
            ->enable('area_childs');

        $filter->setParameter('area', $area->getId(), Type::INTEGER);
    }
}