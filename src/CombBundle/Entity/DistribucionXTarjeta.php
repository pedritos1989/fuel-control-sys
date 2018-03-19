<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * DistribucionXTarjeta
 *
 * @ORM\Table(name="s_distribucion_x_tarjeta")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\DistribucionXTarjetaRepository")
 * @UniqueEntity({"tarjeta","distribucion"})
 * @Assert\Callback("cantidadValida")
 */
class DistribucionXTarjeta
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
     * @var \CombBundle\Entity\Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\Tarjeta", inversedBy="distribuciones")
     * @Assert\NotBlank()
     */
    private $tarjeta;

    /**
     * @var \CombBundle\Entity\Distribucion
     *
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\Distribucion", inversedBy="distTjts")
     */
    private $distribucion;

    /**
     * @var int
     *
     * @ORM\Column(name="asignacion", type="integer")
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     */
    private $asignacion;

    /**
     * @ORM\ManyToMany(targetEntity="CombBundle\Entity\Solicitud", inversedBy="distribuciones")
     * @ORM\JoinTable(name="distribucion_tarjeta_solicitud")
     */
    private $solicitudes;


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return sprintf('%s | %s | %s Lt', $this->getTarjeta(), $this->getDistribucion(), $this->getAsignacion());
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->solicitudes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set asignacion
     *
     * @param integer $asignacion
     * @return DistribucionXTarjeta
     */
    public function setAsignacion($asignacion)
    {
        $this->asignacion = $asignacion;

        return $this;
    }

    /**
     * Get asignacion
     *
     * @return integer
     */
    public function getAsignacion()
    {
        return $this->asignacion;
    }

    /**
     * Set tarjeta
     *
     * @param \CombBundle\Entity\Tarjeta $tarjeta
     * @return DistribucionXTarjeta
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
     * Set distribucion
     *
     * @param \CombBundle\Entity\Distribucion $distribucion
     * @return DistribucionXTarjeta
     */
    public function setDistribucion(\CombBundle\Entity\Distribucion $distribucion = null)
    {
        $this->distribucion = $distribucion;

        return $this;
    }

    /**
     * Get distribucion
     *
     * @return \CombBundle\Entity\Distribucion
     */
    public function getDistribucion()
    {
        return $this->distribucion;
    }

    /**
     * Add solicitudes
     *
     * @param \CombBundle\Entity\Solicitud $solicitudes
     * @return DistribucionXTarjeta
     */
    public function addSolicitude(\CombBundle\Entity\Solicitud $solicitudes)
    {
        $this->solicitudes[] = $solicitudes;

        return $this;
    }

    /**
     * Remove solicitudes
     *
     * @param \CombBundle\Entity\Solicitud $solicitudes
     */
    public function removeSolicitude(\CombBundle\Entity\Solicitud $solicitudes)
    {
        $this->solicitudes->removeElement($solicitudes);
    }

    /**
     * Get solicitudes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSolicitudes()
    {
        return $this->solicitudes;
    }

    // <editor-fold defaultstate="collapsed" desc="Validaciones">
    public function cantidadValida(ExecutionContext $context)
    {
        if ($this->getTarjeta() !== null) {
            $servicio = $this->getTarjeta()->getServicio();
            $cantidades = $this->getDistribucion()->getPlanAsignacion()->getCantidades();
            $depends = $this->getDistribucion()->getDistTjts()->toArray();
            $consumido = 0;
            foreach ($depends as $elem) {
                if ($elem->getTarjeta()->getServicio() === $servicio) {
                    $consumido += $elem->getAsignacion();
                }
            }
            $total = 0;
            foreach ($cantidades as $ctdad) {
                if ($ctdad->getServicio() === $servicio) {
                    $total = $ctdad->getCantidad();
                }
            }

            if ($consumido + $this->getAsignacion() > $total) {
                $context->addViolationAt('asignacion', 'amounts.mismatch');
                return;
            }
            if ($this->getTarjeta()->getAbastecimiento() !== null && $this->getTarjeta()->getAbastecimiento() < $this->getAsignacion()) {
                $context->addViolationAt('asignacion', 'amounts.exceed');
                return;
            }
        }
    }
    // </editor-fold>
}
