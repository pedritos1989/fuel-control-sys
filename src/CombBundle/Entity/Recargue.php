<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Recargue
 *
 * @ORM\Table(name="s_recargue")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\RecargueRepository")
 * @UniqueEntity({"tarjeta","distTrjt"})
 */
class Recargue
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
     * @var Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\Tarjeta")
     */
    private $tarjeta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @var \CombBundle\Entity\DistribucionXTarjeta
     *
     * @ORM\OneToOne(targetEntity="CombBundle\Entity\DistribucionXTarjeta")
     */
    private $distTrjt;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $confirmacion;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $responsable;


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
     * Set tarjeta
     *
     * @param \CombBundle\Entity\Tarjeta $tarjeta
     * @return Recargue
     */
    public function setTarjeta(\CombBundle\Entity\Tarjeta $tarjeta = null)
    {
        $this->tarjeta = $tarjeta;

        return $this;
    }

    /**
     * Get tarjeta
     *
     * @return \CombBundle\Entity\Tarjeta
     */
    public function getTarjeta()
    {
        return $this->tarjeta;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Recargue
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

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return sprintf('%s - %s', $this->getTarjeta(), $this->getFecha()->format('d/m/Y H:i'));
    }

    /**
     * Set distTrjt
     *
     * @param \CombBundle\Entity\DistribucionXTarjeta $distTrjt
     * @return Recargue
     */
    public function setDistTrjt(\CombBundle\Entity\DistribucionXTarjeta $distTrjt = null)
    {
        $this->distTrjt = $distTrjt;

        return $this;
    }

    /**
     * Get distTrjt
     *
     * @return \CombBundle\Entity\DistribucionXTarjeta
     */
    public function getDistTrjt()
    {
        return $this->distTrjt;
    }

    /**
     * Set confirmacion
     *
     * @param boolean $confirmacion
     * @return Recargue
     */
    public function setConfirmacion($confirmacion)
    {
        $this->confirmacion = $confirmacion;

        return $this;
    }

    /**
     * Get confirmacion
     *
     * @return boolean
     */
    public function getConfirmacion()
    {
        return $this->confirmacion;
    }

    /**
     * Set responsable
     *
     * @param string $responsable
     * @return Recargue
     */
    public function setResponsable($responsable)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return string
     */
    public function getResponsable()
    {
        return $this->responsable;
    }
}
