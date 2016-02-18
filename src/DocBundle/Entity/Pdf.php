<?php

namespace DocBundle\Entity;

use Doctrine\DBAL\Types\BlobType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Pdf
 *
 * @ORM\Table(name="pdf", indexes={@ORM\Index(name="current_pdf_idx", columns={"current"})})
 * @ORM\Entity(repositoryClass="DocBundle\Repository\PdfRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Pdf
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
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="current", type="boolean", nullable=true)
     */
    private $current;

    /**
     * @var BlobType
     *
     * @ORM\Column(name="file", type="blob", nullable=false)
     */
    private $file;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="DocBundle\Entity\Parametrage", inversedBy="pdfSources")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $parametrage;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="DocBundle\Entity\ArchiveParam", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $archives;


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
     * Set title
     *
     * @param string $title
     *
     * @return Pdf
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set file
     *
     * @param string $file
     *
     * @return Pdf
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set parametrage
     *
     * @param \DocBundle\Entity\Parametrage $parametrage
     *
     * @return Pdf
     */
    public function setParametrage(\DocBundle\Entity\Parametrage $parametrage)
    {
        $this->parametrage = $parametrage;

        return $this;
    }

    /**
     * Get parametrage
     *
     * @return \DocBundle\Entity\Parametrage
     */
    public function getParametrage()
    {
        return $this->parametrage;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->archives = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add archive
     *
     * @param \DocBundle\Entity\ArchiveParam $archive
     *
     * @return Pdf
     */
    public function addArchive(\DocBundle\Entity\ArchiveParam $archive)
    {
        $this->archives[] = $archive;

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
     * Set current
     *
     * @param boolean $current
     *
     * @return Pdf
     */
    public function setCurrent($current)
    {
        $this->current = $current;

        return $this;
    }

    /**
     * Get current
     *
     * @return boolean
     */
    public function getCurrent()
    {
        return $this->current;
    }
}
