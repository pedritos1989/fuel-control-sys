<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Distribucion
 *
 * @ORM\Table(name="s_distribucion")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\DistribucionRepository")
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
     * @var \CombBundle\Entity\PlanAsignacion
     *
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\PlanAsignacion", inversedBy="distribuciones")
     */
    private $planAsignacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date")
     * @Assert\Date()
     */
    private $fecha;

    /**
     * @ORM\OneToMany(targetEntity="CombBundle\Entity\DistribucionXTarjeta", mappedBy="distribucion")
     */
    private $distTjts;


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return sprintf('%s', $this->getPlanAsignacion());
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->distTjts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Distribucion
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
     * Set planAsignacion
     *
     * @param \CombBundle\Entity\PlanAsignacion $planAsignacion
     *
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
     * Add distTjt
     *
     * @param \CombBundle\Entity\DistribucionXTarjeta $distTjt
     *
     * @return Distribucion
     */
    public function addDistTjt(\CombBundle\Entity\DistribucionXTarjeta $distTjt)
    {
        $this->distTjts[] = $distTjt;

        return $this;
    }

    /**
     * Remove distTjt
     *
     * @param \CombBundle\Entity\DistribucionXTarjeta $distTjt
     */
    public function removeDistTjt(\CombBundle\Entity\DistribucionXTarjeta $distTjt)
    {
        $this->distTjts->removeElement($distTjt);
    }

    /**
     * Get distTjts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDistTjts()
    {
        return $this->distTjts;
    }
}
