<?php

namespace CombBundle\Entity;

use CombBundle\Model\AreaInterface;
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
class PlanAsignacion implements AreaInterface
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
     * @ORM\OneToMany(targetEntity="CombBundle\Entity\CantidadXPlan", mappedBy="plan", cascade="all")
     */
    private $cantidades;

    /**
     * @ORM\OneToMany(targetEntity="CombBundle\Entity\Distribucion", mappedBy="planAsignacion")
     */
    private $distribuciones;


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return sprintf('Fecha de asignación: %s - Área: %s', $this->getFecha()->format('d/m/Y'), $this->getArea());
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
     * Constructor
     */
    public function __construct()
    {
        $this->cantidades = new \Doctrine\Common\Collections\ArrayCollection();
        $this->distribuciones = new \Doctrine\Common\Collections\ArrayCollection();
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
     *
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
     * Set asignacionMensual
     *
     * @param \CombBundle\Entity\AsignacionMensual $asignacionMensual
     *
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
     * Add cantidade
     *
     * @param \CombBundle\Entity\CantidadXPlan $cantidade
     *
     * @return PlanAsignacion
     */
    public function addCantidade(\CombBundle\Entity\CantidadXPlan $cantidade)
    {
        $this->cantidades[] = $cantidade;

        return $this;
    }

    /**
     * Remove cantidade
     *
     * @param \CombBundle\Entity\CantidadXPlan $cantidade
     */
    public function removeCantidade(\CombBundle\Entity\CantidadXPlan $cantidade)
    {
        $this->cantidades->removeElement($cantidade);
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

    /**
     * Add distribucione
     *
     * @param \CombBundle\Entity\Distribucion $distribucione
     *
     * @return PlanAsignacion
     */
    public function addDistribucione(\CombBundle\Entity\Distribucion $distribucione)
    {
        $this->distribuciones[] = $distribucione;

        return $this;
    }

    /**
     * Remove distribucione
     *
     * @param \CombBundle\Entity\Distribucion $distribucione
     */
    public function removeDistribucione(\CombBundle\Entity\Distribucion $distribucione)
    {
        $this->distribuciones->removeElement($distribucione);
    }

    /**
     * Get distribuciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDistribuciones()
    {
        return $this->distribuciones;
    }
}
