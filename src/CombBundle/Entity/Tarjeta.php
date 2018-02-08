<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tarjeta
 *
 * @ORM\Table(name="s_tarjeta")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\TarjetaRepository")
 */
class Tarjeta
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
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=4)
     * @Assert\Length(max=4)
     */
    private $numero;

    /**
     * @var int
     *
     * @ORM\Column(name="lote", type="integer")
     */
    private $lote;

    /**
     * @var int
     *
     * @ORM\Column(name="abastecimiento", type="integer")
     */
    private $abastecimiento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaVenc", type="date")
     */
    private $fechaVenc;

    /**
     * @ORM\ManyToMany(targetEntity="CombBundle\Entity\Distribucion", mappedBy="tarjetas")
     */
    private $distribuciones;

    /**
     * @var Area
     *
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\Area")
     */
    private $area;

    /**
     * @var \NomencladorBundle\Entity\Servicio
     *
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\Servicio")
     */
    private $servicio;


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return sprintf('%s', $this->getNumero());
    }

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set numero
     *
     * @param string $numero
     * @return Tarjeta
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set lote
     *
     * @param integer $lote
     * @return Tarjeta
     */
    public function setLote($lote)
    {
        $this->lote = $lote;

        return $this;
    }

    /**
     * Get lote
     *
     * @return integer
     */
    public function getLote()
    {
        return $this->lote;
    }

    /**
     * Set abastecimiento
     *
     * @param integer $abastecimiento
     * @return Tarjeta
     */
    public function setAbastecimiento($abastecimiento)
    {
        $this->abastecimiento = $abastecimiento;

        return $this;
    }

    /**
     * Get abastecimiento
     *
     * @return integer
     */
    public function getAbastecimiento()
    {
        return $this->abastecimiento;
    }

    /**
     * Set fechaVenc
     *
     * @param \DateTime $fechaVenc
     * @return Tarjeta
     */
    public function setFechaVenc($fechaVenc)
    {
        $this->fechaVenc = $fechaVenc;

        return $this;
    }

    /**
     * Get fechaVenc
     *
     * @return \DateTime
     */
    public function getFechaVenc()
    {
        return $this->fechaVenc;
    }

    /**
     * Add distribuciones
     *
     * @param \CombBundle\Entity\Distribucion $distribuciones
     * @return Tarjeta
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
     * @return Tarjeta
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
     * Set servicio
     *
     * @param \NomencladorBundle\Entity\Servicio $servicio
     * @return Tarjeta
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
