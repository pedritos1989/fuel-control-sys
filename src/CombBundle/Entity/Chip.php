<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * Chip
 *
 * @ORM\Table(name="s_chip")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\ChipRepository")
 * @Assert\Callback("cantidadValida")
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
     * @var int
     *
     * @ORM\Column(name="cantcomb", type="integer")
     * @Assert\NotBlank()
     */
    private $cantcomb;

    /**
     * @var int
     *
     * @ORM\Column(name="saldo_inicial", type="integer")
     * @Assert\NotBlank()
     */
    private $saldoInicial;

    /**
     * @var int
     *
     * @ORM\Column(name="saldo_final", type="integer")
     * @Assert\NotBlank()
     */
    private $saldoFinal;

    /**
     * @var Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\Tarjeta")
     * @Assert\NotNull()
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
     * @param integer $cantcomb
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
     * @return integer
     */
    public function getCantcomb()
    {
        return $this->cantcomb;
    }

    /**
     * Set saldoInicial
     *
     * @param integer $saldoInicial
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
     * @return integer
     */
    public function getSaldoInicial()
    {
        return $this->saldoInicial;
    }

    /**
     * Set saldoFinal
     *
     * @param integer $saldoFinal
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
     * @return integer
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

    // <editor-fold defaultstate="collapsed" desc="Validaciones">
    public function cantidadValida(ExecutionContext $context)
    {
        if ($this->getSaldoInicial() - $this->getCantcomb() < 0) {
            $context->addViolationAt('cantcomb', 'amounts.mismatch');
            return;
        }
    }
    // </editor-fold>
}
