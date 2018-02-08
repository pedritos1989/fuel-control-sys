<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Chip
 *
 * @ORM\Table(name="s_chip")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\ChipRepository")
 */
class Chip
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
     * @ORM\Column(name="fecha", type="datetime")
     * @Assert\DateTime()
     * @Assert\NotBlank()
     */
    private $fecha;

    /**
     * @var float
     *
     * @ORM\Column(name="cantcomb", type="float")
     * @Assert\NotBlank()
     */
    private $cantcomb;

    /**
     * @var float
     *
     * @ORM\Column(name="saldo_inicial", type="float")
     * @Assert\NotBlank()
     */
    private $saldoInicial;

    /**
     * @var float
     *
     * @ORM\Column(name="saldo_final", type="float")
     * @Assert\NotBlank()
     */
    private $saldoFinal;

    /**
     * @var Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\Tarjeta")
     */
    private $tarjeta;


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return sprintf('%s -> %s', $this->getTarjeta(), $this->getFecha()->format('d/m/Y H:i'));
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
     * @return Chip
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
     * Set cantcomb
     *
     * @param float $cantcomb
     * @return Chip
     */
    public function setCantcomb($cantcomb)
    {
        $this->cantcomb = $cantcomb;

        return $this;
    }

    /**
     * Get cantcomb
     *
     * @return float
     */
    public function getCantcomb()
    {
        return $this->cantcomb;
    }

    /**
     * Set saldoInicial
     *
     * @param float $saldoInicial
     * @return Chip
     */
    public function setSaldoInicial($saldoInicial)
    {
        $this->saldoInicial = $saldoInicial;

        return $this;
    }

    /**
     * Get saldoInicial
     *
     * @return float
     */
    public function getSaldoInicial()
    {
        return $this->saldoInicial;
    }

    /**
     * Set saldoFinal
     *
     * @param float $saldoFinal
     * @return Chip
     */
    public function setSaldoFinal($saldoFinal)
    {
        $this->saldoFinal = $saldoFinal;

        return $this;
    }

    /**
     * Get saldoFinal
     *
     * @return float
     */
    public function getSaldoFinal()
    {
        return $this->saldoFinal;
    }

    /**
     * Set tarjeta
     *
     * @param \CombBundle\Entity\Tarjeta $tarjeta
     * @return Chip
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
