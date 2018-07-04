<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AsignacionXServicio
 *
 * @ORM\Table(name="s_asignacion_x_servicio")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\AsignacionXServicioRepository")
 * @UniqueEntity({"servicio","asignacionMensual"})
 */
class AsignacionXServicio
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
     * @ORM\Column(name="cantidad", type="integer")
     * @Assert\NotBlank()
     */
    private $cantidad;

    /**
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\Servicio")
     */
    private $servicio;

    /**
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\AsignacionMensual", inversedBy="cantidades")
     */
    private $asignacionMensual;


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
     * Set cantidad
     *
     * @param integer $cantidad
     * @return AsignacionXServicio
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set servicio
     *
     * @param \NomencladorBundle\Entity\Servicio $servicio
     * @return AsignacionXServicio
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

    /**
     * Set asignacionMensual
     *
     * @param \CombBundle\Entity\AsignacionMensual $asignacionMensual
     * @return AsignacionXServicio
     */
    public function setAsignacionMensual(\CombBundle\Entity\AsignacionMensual $asignacionMensual = null)
    {
        $this->asignacionMensual = $asignacionMensual;

        return $this;
    }

    /**
     * Get asignacionMensual
     *
     * @return \CombBundle\Entity\AsignacionMensual
     */
    public function getAsignacionMensual()
    {
        return $this->asignacionMensual;
    }
}
