<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * CantidadXPlan
 *
 * @ORM\Table(name="s_cantidad_x_plan")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\CantidadXPlanRepository")
 * @UniqueEntity({"servicio","plan"})
 * @Assert\Callback("cantidadValida")
 */
class CantidadXPlan
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
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float")
     * @Assert\NotBlank()
     */
    private $cantidad;

    /**
     * @ORM\ManyToOne(targetEntity="CombBundle\Entity\PlanAsignacion", inversedBy="cantidades")
     */
    private $plan;

    /**
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\Servicio")
     * @Assert\NotNull()
     */
    private $servicio;


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
     * @param float $cantidad
     * @return CantidadXPlan
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return float
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set plan
     *
     * @param \CombBundle\Entity\PlanAsignacion $plan
     * @return CantidadXPlan
     */
    public function setPlan(\CombBundle\Entity\PlanAsignacion $plan = null)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get plan
     *
     * @return \CombBundle\Entity\PlanAsignacion
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Set servicio
     *
     * @param \NomencladorBundle\Entity\Servicio $servicio
     * @return CantidadXPlan
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

    // <editor-fold defaultstate="collapsed" desc="Validaciones">
    public function cantidadValida(ExecutionContext $context)
    {
        $asignacionMensual = $this->getPlan()->getAsignacionMensual();
        $planes = $asignacionMensual->getPlan()->toArray();
        $asignaciones = $asignacionMensual->getCantidades()->toArray();
        $consumido = 0;
        foreach ($planes as $plan) {
            $cantidades = $plan->getCantidades()->toArray();
            foreach ($cantidades as $ctdad) {
                if ($ctdad->getServicio() === $this->getServicio()) {
                    $consumido += $ctdad->getCantidad();
                }
            }
        }
        $total = 0;
        foreach ($asignaciones as $asignacion) {
            if ($asignacion->getServicio() === $this->getServicio()) {
                $total = $asignacion->getCantidad();
            }
        }
        if ($consumido + $this->getCantidad() > $total) {
            $context->addViolationAt('cantidad', 'amounts.mismatch', array(), null);
            return;
        }
    }
    // </editor-fold>

}
