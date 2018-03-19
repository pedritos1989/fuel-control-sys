<?php

namespace UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dundivet\UploadBundle\Model\UploadAbstract;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ClientAvatar
 *
 * @ORM\Table(name="s_client_avatar")
 * @ORM\Entity()
 */
class ClientAvatar extends UploadAbstract
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
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     *
     * @Assert\File(
     *     maxSize="2M",
     *     mimeTypes={
     *          "image/jpeg", "image/png"
     *     }
     * )
     * @Assert\NotNull()
     */
    protected $file;

    /**
     * @ORM\OneToOne(targetEntity="UsuarioBundle\Entity\User", inversedBy="avatar")
     */
    private $client;

    /**
     * @ORM\Column(name="picture", type="blob", nullable=true)
     */
    private $picture;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set client
     *
     * @param \UsuarioBundle\Entity\User $client
     *
     * @return ClientAvatar
     */
    public function setClient(\UsuarioBundle\Entity\User $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \UsuarioBundle\Entity\User
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return ClientAvatar
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Upload action, save file into resources upload dir
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        $this->picture = file_get_contents($this->getFile());

        if (isset($this->temp)) {
            $this->temp = null;
        }

        $this->file = null;
    }

    /**
     * Get web path.
     *
     * @return string
     */
    public function getWebPath()
    {
        return null === $this->path || $this->picture === null
            ? null
            : base64_encode(stream_get_contents($this->picture));
    }
}
