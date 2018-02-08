<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Solicitud
 *
 * @ORM\Table(name="s_solicitud")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\SolicitudRepository")
 */
class Solicitud
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
     * @ORM\Column(name="fechaHoraS", type="datetime")
     */
    private $fechaHoraS;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaHoraR", type="datetime")
     */
    private $fechaHoraR;

    /**
     * @var string
     *
     * @ORM\Column(name="lugar", type="string", length=255)
     */
    private $lugar;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo", type="text")
     */
    private $motivo;

    /**
     * @var int
     *
     * @ORM\Column(name="cantpersona", type="integer")
     */
    private $cantpersona;

    /**
     * @ORM\ManyToMany(targetEntity="CombBundle\Entity\Distribucion", mappedBy="solicitudes")
     */
    private $distribuciones;

    /**
     * @var Area
     *
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\Area")
     */
    private $area;


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return sprintf('%s -> %s', $this->getLugar(), $this->getMotivo());
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
     * Set fechaHoraS
     *
     * @param \DateTime $fechaHoraS
     * @return Solicitud
     */
    public function setFechaHoraS($fechaHoraS)
    {
        $this->fechaHoraS = $fechaHoraS;

        return $this;
    }

    /**
     * Get fechaHoraS
     *
     * @return \DateTime
     */
    public function getFechaHoraS()
    {
        return $this->fechaHoraS;
    }

    /**
     * Set fechaHoraR
     *
     * @param \DateTime $fechaHoraR
     * @return Solicitud
     */
    public function setFechaHoraR($fechaHoraR)
    {
        $this->fechaHoraR = $fechaHoraR;

        return $this;
    }

    /**
     * Get fechaHoraR
     *
     * @return \DateTime
     */
    public function getFechaHoraR()
    {
        return $this->fechaHoraR;
    }

    /**
     * Set lugar
     *
     * @param string $lugar
     * @return Solicitud
     */
    public function setLugar($lugar)
    {
        $this->lugar = $lugar;

        return $this;
    }

    /**
     * Get lugar
     *
     * @return string
     */
    public function getLugar()
    {
        return $this->lugar;
    }

    /**
     * Set motivo
     *
     * @param string $motivo
     * @return Solicitud
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;

        return $this;
    }

    /**
     * Get motivo
     *
     * @return string
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set cantpersona
     *
     * @param integer $cantpersona
     * @return Solicitud
     */
    public function setCantpersona($cantpersona)
    {
        $this->cantpersona = $cantpersona;

        return $this;
    }

    /**
     * Get cantpersona
     *
     * @return integer
     */
    public function getCantpersona()
    {
        return $this->cantpersona;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->distribuciones = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add distribuciones
     *
     * @param \CombBundle\Entity\Distribucion $distribuciones
     * @return Solicitud
     */
    public function addDistribucione(\CombBundle\Entity\Distribucion $distribuciones)
    {
        $this->distribuciones[] = $distribuciones;

        return $this;
    }

    /**
     * Remove distribuciones
     *
     * @param \CombBundle\Entity\Distribucion $distribuciones
     */
    public function removeDistribucione(\CombBundle\Entity\Distribucion $distribuciones)
    {
        $this->distribuciones->removeElement($distribuciones);
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

    /**
     * Set area
     *
     * @param \CombBundle\Entity\Area $area
     * @return Solicitud
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
}
