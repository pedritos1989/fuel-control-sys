<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Carro
 *
 * @ORM\Table(name="s_carro")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\CarroRepository")
 * @UniqueEntity({"chofer"})
 * @UniqueEntity({"matricula"})
 */
class Carro
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
     * @ORM\Column(name="matricula", type="string", length=10)
     * @Assert\NotBlank()
     * @Assert\Length(max="6")
     */
    private $matricula;

    /**
     * @var string
     *
     * @ORM\Column(name="modelo", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $modelo;

    /**
     * @var string
     *
     * @ORM\Column(name="insptecn", type="string", length=255)
     */
    private $insptecn;

    /**
     * @var float
     *
     * @ORM\Column(name="indcons", type="float")
     */
    private $indcons;

    /**
     * @var \CombBundle\Entity\Area
     *
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\Area")
     */
    private $area;

    /**
     * @var \CombBundle\Entity\Chofer
     *
     * @ORM\OneToOne(targetEntity="CombBundle\Entity\Chofer")
     */
    private $chofer;

    /**
     * @var \NomencladorBundle\Entity\EstadoCarro
     *
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\EstadoCarro")
     */
    private $estado;

    /**
     * @var \NomencladorBundle\Entity\TipoCarro
     *
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\TipoCarro")
     */
    private $tipo;

    /**
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\Tarjeta", inversedBy="carros")
     */
    private $tarjeta;


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return sprintf('%s', $this->getMatricula());
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
     * Set matricula
     *
     * @param string $matricula
     * @return Carro
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;

        return $this;
    }

    /**
     * Get matricula
     *
     * @return string
     */
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * Set modelo
     *
     * @param string $modelo
     * @return Carro
     */
    public function setModelo($modelo)
    {
        $this->modelo = $modelo;

        return $this;
    }

    /**
     * Get modelo
     *
     * @return string
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * Set insptecn
     *
     * @param string $insptecn
     * @return Carro
     */
    public function setInsptecn($insptecn)
    {
        $this->insptecn = $insptecn;

        return $this;
    }

    /**
     * Get insptecn
     *
     * @return string
     */
    public function getInsptecn()
    {
        return $this->insptecn;
    }

    /**
     * Set indcons
     *
     * @param float $indcons
     * @return Carro
     */
    public function setIndcons($indcons)
    {
        $this->indcons = $indcons;

        return $this;
    }

    /**
     * Get indcons
     *
     * @return float
     */
    public function getIndcons()
    {
        return $this->indcons;
    }

    /**
     * Set area
     *
     * @param \CombBundle\Entity\Area $area
     * @return Carro
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
     * Set chofer
     *
     * @param \CombBundle\Entity\Chofer $chofer
     * @return Carro
     */
    public function setChofer(\CombBundle\Entity\Chofer $chofer = null)
    {
        $this->chofer = $chofer;

        return $this;
    }

    /**
     * Get chofer
     *
     * @return \CombBundle\Entity\Chofer
     */
    public function getChofer()
    {
        return $this->chofer;
    }

    /**
     * Set estado
     *
     * @param \NomencladorBundle\Entity\EstadoCarro $estado
     * @return Carro
     */
    public function setEstado(\NomencladorBundle\Entity\EstadoCarro $estado = null)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return \NomencladorBundle\Entity\EstadoCarro
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set tipo
     *
     * @param \NomencladorBundle\Entity\TipoCarro $tipo
     * @return Carro
     */
    public function setTipo(\NomencladorBundle\Entity\TipoCarro $tipo = null)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \NomencladorBundle\Entity\TipoCarro
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set tarjeta
     *
     * @param \CombBundle\Entity\Tarjeta $tarjeta
     * @return Carro
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

}
