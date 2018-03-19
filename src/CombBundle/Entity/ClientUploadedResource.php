<?php

namespace CombBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dundivet\UploadBundle\Model\UploadAbstract;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ClientUploadedResource.
 *
 * @ORM\Table(name="s_ley_store")
 * @ORM\Entity(repositoryClass="CombBundle\Repository\ClientUploadedResourceRepository")
 */
class ClientUploadedResource extends UploadAbstract
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
     *          "text/plain", "application/pdf", "application/msword", "image/gif",
     *          "image/jpeg", "image/png", "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
     *          "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *          "application/wps-office.xlsx", "application/zip", "application/x-compressed-zip",
     *          "application/x-rar-compressed"
     *     }
     * )
     * @Assert\NotNull()
     */
    protected $file;

    /**
     * @var \UsuarioBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="UsuarioBundle\Entity\User")
     */
    private $client;

    /**
     * @var string
     *
     * @ORM\Column(name="attachment_name", type="string")
     * @Assert\NotBlank()
     */
    private $attachmentName;


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
     * Set attachmentName
     *
     * @param string $attachmentName
     * @return ClientUploadedResource
     */
    public function setAttachmentName($attachmentName)
    {
        $this->attachmentName = $attachmentName;

        return $this;
    }

    /**
     * Get attachmentName
     *
     * @return string
     */
    public function getAttachmentName()
    {
        return $this->attachmentName;
    }

    /**
     * Set client
     *
     * @param \UsuarioBundle\Entity\User $client
     * @return ClientUploadedResource
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
}
