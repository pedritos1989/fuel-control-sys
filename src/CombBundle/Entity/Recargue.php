<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recargue
 *
 * @ORM\Table(name="s_recargue")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\RecargueRepository")
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
}
