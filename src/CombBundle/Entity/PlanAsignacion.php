<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PlanAsignacion
 *
 * @ORM\Table(name="s_plan_asignacion")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\PlanAsignacionRepository")
 * @UniqueEntity({"area","asignacionMensual"})
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
     * @Assert\Date()
     * @Assert\NotBlank()
     */
    private $fecha;

    /**
     * @var Area
     *
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\Area")
     */
    private $area;

    /**
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\AsignacionMensual", inversedBy="plan")
     */
    private $asignacionMensual;

    /**
     * @ORM\OneToMany(targetEntity="CombBundle\Entity\CantidadXPlan", mappedBy="plan")
     */
    private $cantidades;


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return sprintf('%s', $this->getFecha()->format('d/m/Y'));
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cantidades = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set area
     *
     * @param \CombBundle\Entity\Area $area
     * @return PlanAsignacion
     */
    public function setArea(\CombBundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \CombBundle\Entity\Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set asignacionMensual
     *
     * @param \CombBundle\Entity\AsignacionMensual $asignacionMensual
     * @return PlanAsignacion
     */
    public function setAsignacionMensual(\CombBundle\Entity\AsignacionMensual $asignacionMensual = null)
    {
        $this->asignacionMensual = $asignacionMensual;

        return $this;
    }

    /**
     * Get asignacionMensual
     *
     * @return \CombBundle\Entity\AsignacionMensual
     */
    public function getAsignacionMensual()
    {
        return $this->asignacionMensual;
    }

    /**
     * Add cantidades
     *
     * @param \CombBundle\Entity\CantidadXPlan $cantidades
     * @return PlanAsignacion
     */
    public function addCantidade(\CombBundle\Entity\CantidadXPlan $cantidades)
    {
        $this->cantidades[] = $cantidades;

        return $this;
    }

    /**
     * Remove cantidades
     *
     * @param \CombBundle\Entity\CantidadXPlan $cantidades
     */
    public function removeCantidade(\CombBundle\Entity\CantidadXPlan $cantidades)
    {
        $this->cantidades->removeElement($cantidades);
    }

    /**
     * Get cantidades
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCantidades()
    {
        return $this->cantidades;
    }
}
