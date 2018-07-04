<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Grupo
 *
 * @ORM\Table(name="n_tipo_carro")
 * @ORM\Entity
 */
class TipoCarro extends BaseNomenclador
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="CombBundle\Entity\Carro", mappedBy="tipo")
     */
    private $carros;


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
     * Constructor
     */
    public function __construct()
    {
        $this->carros = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add carro
     *
     * @param \CombBundle\Entity\Carro $carro
     *
     * @return TipoCarro
     */
    public function addCarro(\CombBundle\Entity\Carro $carro)
    {
        $this->carros[] = $carro;

        return $this;
    }

    /**
     * Remove carro
     *
     * @param \CombBundle\Entity\Carro $carro
     */
    public function removeCarro(\CombBundle\Entity\Carro $carro)
    {
        $this->carros->removeElement($carro);
    }

    /**
     * Get carros
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCarros()
    {
        return $this->carros;
    }

}
