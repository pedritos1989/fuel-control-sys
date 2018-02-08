<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\Servicio;

/**
 * PlanAsignacion
 *
 * @ORM\Table(name="s_plan_asignacion")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\PlanAsignacionRepository")
 */
class PlanAsignacion
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date")
     */
    private $fecha;

    /**
     * @var int
     *
     * @ORM\Column(name="cantcomb", type="integer")
     */
    private $cantcomb;

    /**
     * @var Servicio
     *
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\Servicio")
     */
    private $servicio;


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return sprintf('%s -> %s', $this->getCantcomb(), $this->getFecha()->format('d/m/Y'));
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return PlanAsignacion
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set cantcomb
     *
     * @param integer $cantcomb
     * @return PlanAsignacion
     */
    public function setCantcomb($cantcomb)
    {
        $this->cantcomb = $cantcomb;

        return $this;
    }

    /**
     * Get cantcomb
     *
     * @return integer
     */
    public function getCantcomb()
    {
        return $this->cantcomb;
    }

    /**
     * Set servicio
     *
     * @param \NomencladorBundle\Entity\Servicio $servicio
     * @return PlanAsignacion
     */
    public function setServicio(\NomencladorBundle\Entity\Servicio $servicio = null)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return \NomencladorBundle\Entity\Servicio
     */
    public function getServicio()
    {
        return $this->servicio;
    }
}
