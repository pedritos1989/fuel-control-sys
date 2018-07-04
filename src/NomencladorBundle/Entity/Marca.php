<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Marca
 *
 * @ORM\Table(name="s_marca")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Repository\MarcaRepository")
 */
class Marca extends BaseNomenclador
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}

