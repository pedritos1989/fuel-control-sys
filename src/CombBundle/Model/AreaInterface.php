<?php

namespace CombBundle\Model;

use CombBundle\Entity\Area;

interface AreaInterface
{
    public function getArea();

    public function setArea(Area $area);
}