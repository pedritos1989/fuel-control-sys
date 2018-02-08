<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * BaseNomenclador
 *
 * @UniqueEntity(fields={"valor"})
 */
class BaseNomenclador
{

    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="string", length=100)
     */
    protected $valor;

    /**
     * Set valor
     *
     * @param string $valor
     * @return BaseNomenclador
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->valor;
    }
}
