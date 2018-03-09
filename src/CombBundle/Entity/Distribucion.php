<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Distribucion
 *
 * @ORM\Table(name="s_distribucion")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\DistribucionRepository")
 * @UniqueEntity("planAsignacion")
 */
class Distribucion
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
     * @ORM\Column(name="plan", type="integer")
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     */
    private $plan;

    /**
     * @var int
     *
     * @ORM\Column(name="asignacion", type="integer")
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     */
    private $asignacion;

    /**
     * @ORM\ManyToMany(targetEntity="CombBundle\Entity\Solicitud", inversedBy="distribuciones")
     * @ORM\JoinTable(name="distribucion_solicitud")
     */
    private $solicitudes;

    /**
     * @var PlanAsignacion
     *
     * @ORM\OneToOne(targetEntity="CombBundle\Entity\PlanAsignacion")
     */
    private $planAsignacion;

    /**
     * @ORM\ManyToMany(targetEntity="CombBundle\Entity\Tarjeta", inversedBy="distribuciones")
     * @ORM\JoinTable(name="distribucion_tarjeta")
     */
    private $tarjetas;

    /**
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\Servicio")
     */
    private $servicio;


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return sprintf('%s -> %s', $this->getPlan(), $this->getAsignacion());
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->solicitudes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tarjetas = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set plan
     *
     * @param integer $plan
     * @return Distribucion
     */
    public function setPlan($plan)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get plan
     *
     * @return integer
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Set asignacion
     *
     * @param integer $asignacion
     * @return Distribucion
     */
    public function setAsignacion($asignacion)
    {
        $this->asignacion = $asignacion;

        return $this;
    }

    /**
     * Get asignacion
     *
     * @return integer
     */
    public function getAsignacion()
    {
        return $this->asignacion;
    }

    /**
     * Add solicitudes
     *
     * @param \CombBundle\Entity\Solicitud $solicitudes
     * @return Distribucion
     */
    public function addSolicitude(\CombBundle\Entity\Solicitud $solicitudes)
    {
        $this->solicitudes[] = $solicitudes;

        return $this;
    }

    /**
     * Remove solicitudes
     *
     * @param \CombBundle\Entity\Solicitud $solicitudes
     */
    public function removeSolicitude(\CombBundle\Entity\Solicitud $solicitudes)
    {
        $this->solicitudes->removeElement($solicitudes);
    }

    /**
     * Get solicitudes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSolicitudes()
    {
        return $this->solicitudes;
    }

    /**
     * Set planAsignacion
     *
     * @param \CombBundle\Entity\PlanAsignacion $planAsignacion
     * @return Distribucion
     */
    public function setPlanAsignacion(\CombBundle\Entity\PlanAsignacion $planAsignacion = null)
    {
        $this->planAsignacion = $planAsignacion;

        return $this;
    }

    /**
     * Get planAsignacion
     *
     * @return \CombBundle\Entity\PlanAsignacion
     */
    public function getPlanAsignacion()
    {
        return $this->planAsignacion;
    }

    /**
     * Add tarjetas
     *
     * @param \CombBundle\Entity\Tarjeta $tarjetas
     * @return Distribucion
     */
    public function addTarjeta(\CombBundle\Entity\Tarjeta $tarjetas)
    {
        $this->tarjetas[] = $tarjetas;

        return $this;
    }

    /**
     * Remove tarjetas
     *
     * @param \CombBundle\Entity\Tarjeta $tarjetas
     */
    public function removeTarjeta(\CombBundle\Entity\Tarjeta $tarjetas)
    {
        $this->tarjetas->removeElement($tarjetas);
    }

    /**
     * Get tarjetas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTarjetas()
    {
        return $this->tarjetas;
    }

    /**
     * Set servicio
     *
     * @param \NomencladorBundle\Entity\Servicio $servicio
     * @return Distribucion
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
