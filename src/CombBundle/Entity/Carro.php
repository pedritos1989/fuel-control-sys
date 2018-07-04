<?php

namespace CombBundle\Entity;

use CombBundle\Model\AreaInterface;
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
 * @UniqueEntity({"tarjeta"})
 */
class Carro implements AreaInterface
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
     * @Assert\Length(max="7")
     */
    private $matricula;

    /**
     * @var \NomencladorBundle\Entity\Marca
     *
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\Marca")
     * @Assert\NotNull()
     */
    private $marca;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="insptecn", type="date", length=255)
     * @Assert\Date()
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
     * @Assert\NotBlank()
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
     * @Assert\NotNull()
     */
    private $estado;

    /**
     * @var \NomencladorBundle\Entity\TipoCarro
     *
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\TipoCarro", inversedBy="carros")
     */
    private $tipo;

    /**
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\Tarjeta", inversedBy="carros")
     * @Assert\NotNull()
     */
    private $tarjeta;


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return sprintf('%s', $this->getMatricula());
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
     *
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
     * Set insptecn
     *
     * @param \DateTime $insptecn
     *
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
     * @return \DateTime
     */
    public function getInsptecn()
    {
        return $this->insptecn;
    }

    /**
     * Set indcons
     *
     * @param float $indcons
     *
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
     * Set marca
     *
     * @param \NomencladorBundle\Entity\Marca $marca
     *
     * @return Carro
     */
    public function setMarca(\NomencladorBundle\Entity\Marca $marca = null)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get marca
     *
     * @return \NomencladorBundle\Entity\Marca
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set chofer
     *
     * @param \CombBundle\Entity\Chofer $chofer
     *
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
     *
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
     *
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
     *
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
