<?php

namespace UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="seg_user")
 * @ORM\Entity(repositoryClass="UsuarioBundle\Repository\UserRepository")
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"email"})
 */
class User extends BaseUser
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
     * @var string
     *
     * @Assert\Email()
     */
    protected $email;

    /**
     * @ORM\ManyToMany(targetEntity="UsuarioBundle\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @ORM\OneToOne(targetEntity="UsuarioBundle\Entity\ClientAvatar", mappedBy="client", cascade={"persist","remove"})
     */
    private $avatar;


    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Set avatar
     *
     * @param \UsuarioBundle\Entity\ClientAvatar $avatar
     *
     * @return User
     */
    public function setAvatar(\UsuarioBundle\Entity\ClientAvatar $avatar = null)
    {
        $avatar->setClient($this);
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return \UsuarioBundle\Entity\ClientAvatar
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
}
