<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AsignacionMensual
 *
 * @ORM\Table(name="s_asignacion_mensual")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\AsignacionMensualRepository")
 * @UniqueEntity({"consecutivo","fecha"})
 */
class AsignacionMensual
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
     * @var int
     *
     * @ORM\Column(name="consecutivo", type="integer")
     * @Assert\NotBlank()
     */
    private $consecutivo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date")
     * @Assert\Date()
     * @Assert\NotBlank()
     */
    private $fecha;

    /**
     * @ORM\OneToMany(targetEntity="CombBundle\Entity\PlanAsignacion", mappedBy="asignacionMensual")
     */
    private $plan;

    /**
     * @ORM\OneToMany(targetEntity="CombBundle\Entity\AsignacionXServicio", cascade={"persist","remove"}, mappedBy="asignacionMensual")
     */
    private $cantidades;


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return sprintf('%s - %s', $this->fecha->format('d/m/Y'), $this->consecutivo);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->plan = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set consecutivo
     *
     * @param integer $consecutivo
     * @return AsignacionMensual
     */
    public function setConsecutivo($consecutivo)
    {
        $this->consecutivo = $consecutivo;

        return $this;
    }

    /**
     * Get consecutivo
     *
     * @return integer
     */
    public function getConsecutivo()
    {
        return $this->consecutivo;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return AsignacionMensual
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
     * Add plan
     *
     * @param \CombBundle\Entity\PlanAsignacion $plan
     * @return AsignacionMensual
     */
    public function addPlan(\CombBundle\Entity\PlanAsignacion $plan)
    {
        $this->plan[] = $plan;

        return $this;
    }

    /**
     * Remove plan
     *
     * @param \CombBundle\Entity\PlanAsignacion $plan
     */
    public function removePlan(\CombBundle\Entity\PlanAsignacion $plan)
    {
        $this->plan->removeElement($plan);
    }

    /**
     * Get plan
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Add cantidades
     *
     * @param \CombBundle\Entity\AsignacionXServicio $cantidades
     * @return AsignacionMensual
     */
    public function addCantidade(\CombBundle\Entity\AsignacionXServicio $cantidades)
    {
        $this->cantidades[] = $cantidades;

        return $this;
    }

    /**
     * Remove cantidades
     *
     * @param \CombBundle\Entity\AsignacionXServicio $cantidades
     */
    public function removeCantidade(\CombBundle\Entity\AsignacionXServicio $cantidades)
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
