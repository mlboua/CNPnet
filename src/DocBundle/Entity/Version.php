<?php

namespace DocBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Version
 *
 * @ORM\Table(name="version")
 * @ORM\Entity(repositoryClass="DocBundle\Repository\VersionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Version
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
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=255, unique=false)
     */
    private $numero;

    /**
     * @var bool
     *
     * @ORM\Column(name="en_cours", type="boolean", nullable=true)
     */
    private $enCours;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", nullable=true)
     */
    private $user;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="DocBundle\Entity\Reseau", cascade={"persist", "remove"}, inversedBy="versions")
     * @ORM\JoinColumn(nullable=true)
     */
    private $reseau;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="DocBundle\Entity\ArchiveParam", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $archives;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;


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
     * Set numero
     *
     * @param string $numero
     *
     * @return Version
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set enCours
     *
     * @param boolean $enCours
     *
     * @return Version
     */
    public function setEnCours($enCours)
    {
        $this->enCours = $enCours;

        return $this;
    }

    /**
     * Get enCours
     *
     * @return bool
     */
    public function getEnCours()
    {
        return $this->enCours;
    }

    /**
     * Set reseau
     *
     * @param \DocBundle\Entity\Reseau $reseau
     *
     * @return Version
     */
    public function setReseau(\DocBundle\Entity\Reseau $reseau = null)
    {
        $this->reseau = $reseau;

        return $this;
    }

    /**
     * Get reseau
     *
     * @return \DocBundle\Entity\Reseau
     */
    public function getReseau()
    {
        return $this->reseau;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Version
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return Version
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->archives = new ArrayCollection();
    }

    /**
     * Add archive
     *
     * @param \DocBundle\Entity\ArchiveParam $archive
     *
     * @return Version
     */
    public function addArchive(\DocBundle\Entity\ArchiveParam $archive)
    {
        $this->archives[] = $archive;

        return $this;
    }

    /**
     * Replace current arcihves with a new archives collection
     *
     * @param ArrayCollection $archives
     * @return $this
     */
    public function addGroupArchives($archives)
    {
        foreach ($archives as $elt) {
            $this->addArchive($elt);
            $elt->addVersion($this);
        }
        return $this;
    }

    /**
     * Remove archive
     *
     * @param \DocBundle\Entity\ArchiveParam $archive
     */
    public function removeArchive(\DocBundle\Entity\ArchiveParam $archive)
    {
        $this->archives->removeElement($archive);
    }

    /**
     * Get archives
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArchives()
    {
        return $this->archives;
    }

    /**
     * @ORM\PrePersist()
     */
    public function createdDate()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function UpdateDate()
    {
        $this->updatedAt = new DateTime();
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Version
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Version
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
