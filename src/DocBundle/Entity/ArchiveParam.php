<?php

namespace DocBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * ArchiveParam
 *
 * @ORM\Table(name="archive_param")
 * @ORM\Entity(repositoryClass="DocBundle\Repository\ArchiveParamRepository")
 */
class ArchiveParam
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
     * @var int
     *
     * @ORM\Column(name="id_param", type="integer")
     */
    private $idParam;

    /**
     * @var Pdf
     *
     * @ORM\ManyToMany(targetEntity="DocBundle\Entity\Pdf", cascade={"persist"})
     */
    private $pdfSources;

    /**
     * @var string
     *
     * @ORM\Column(name="partenaires", type="string", length=255)
     */
    private $partenaires;

    /**
     * @var string
     *
     * @ORM\Column(name="collectivites", type="string", length=255)
     */
    private $collectivites;

    /**
     * @var string
     *
     * @ORM\Column(name="contrat", type="string", length=255)
     */
    private $contrat;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="ordre", type="string", length=255)
     */
    private $ordre;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="text", nullable=true)
     */
    private $action;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="DocBundle\Entity\Version")
     * @ORM\JoinColumn(nullable=true)
     */
    private $versions;


    private $currentPdf;


    /**
     * @param Parametrage $parametrage
     */
    public function setParametrage(Parametrage $parametrage)
    {
        $this->setIdParam($parametrage->getId());
        $this->setContrat($parametrage->getContrat());
        $this->setLibelle($parametrage->getLibelle());
        $this->setType($parametrage->getType());
        $this->setPartenaires($parametrage->getPartenaires());
        $this->setCollectivites($parametrage->getCollectivites());
        $this->setOrdre($parametrage->getOrdre());
        $this->setReference($parametrage->getReference());
        $this->setCreatedAt(new DateTime());
    }


    /**
     * @param $extension
     * @return string
     */
    public function generateFileName($extension)
    {
        $fileName = $this->getContrat().'_'.$this->getType().'_'.$this->getReference().''.$extension;
        return $fileName;
    }

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
     * Set id
     *
     * @return ArchiveParam
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set action
     *
     * @param \DateTime $action
     *
     * @return ArchiveParam
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return \DateTime
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ArchiveParam
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
     * Set partenaires
     *
     * @param string $partenaires
     *
     * @return ArchiveParam
     */
    public function setPartenaires($partenaires)
    {
        $this->partenaires = $partenaires;

        return $this;
    }

    /**
     * Get partenaires
     *
     * @return string
     */
    public function getPartenaires()
    {
        return $this->partenaires;
    }

    /**
     * Set collectivites
     *
     * @param string $collectivites
     *
     * @return ArchiveParam
     */
    public function setCollectivites($collectivites)
    {
        $this->collectivites = $collectivites;

        return $this;
    }

    /**
     * Get collectivites
     *
     * @return string
     */
    public function getCollectivites()
    {
        return $this->collectivites;
    }

    /**
     * Set contrat
     *
     * @param string $contrat
     *
     * @return ArchiveParam
     */
    public function setContrat($contrat)
    {
        $this->contrat = $contrat;

        return $this;
    }

    /**
     * Get contrat
     *
     * @return string
     */
    public function getContrat()
    {
        return $this->contrat;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return ArchiveParam
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set ordre
     *
     * @param string $ordre
     *
     * @return ArchiveParam
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return string
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return ArchiveParam
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return ArchiveParam
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set idParam
     *
     * @param integer $idParam
     *
     * @return ArchiveParam
     */
    public function setIdParam($idParam)
    {
        $this->idParam = $idParam;

        return $this;
    }

    /**
     * Get idParam
     *
     * @return integer
     */
    public function getIdParam()
    {
        return $this->idParam;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pdfSources = new ArrayCollection();
        $this->versions = new ArrayCollection();
    }

    /**
     * Add pdfSource
     *
     * @param \DocBundle\Entity\Pdf $pdfSource
     *
     * @return ArchiveParam
     */
    public function addPdfSource(\DocBundle\Entity\Pdf $pdfSource)
    {
        $this->pdfSources[] = $pdfSource;

        return $this;
    }

    /**
     * Remove pdfSource
     *
     * @param \DocBundle\Entity\Pdf $pdfSource
     */
    public function removePdfSource(\DocBundle\Entity\Pdf $pdfSource)
    {
        $this->pdfSources->removeElement($pdfSource);
    }

    /**
     * Get pdfSources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPdfSources()
    {
        return $this->pdfSources;
    }

    /**
     * Add version
     *
     * @param \DocBundle\Entity\Version $version
     *
     * @return ArchiveParam
     */
    public function addVersion(\DocBundle\Entity\Version $version)
    {
        $this->versions[] = $version;

        return $this;
    }

    /**
     * Remove version
     *
     * @param \DocBundle\Entity\Version $version
     */
    public function removeVersion(\DocBundle\Entity\Version $version)
    {
        $this->versions->removeElement($version);
    }

    /**
     * Get versions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @return mixed
     */
    public function getCurrentPdf()
    {
        return $this->currentPdf;
    }

    /**
     * @param mixed $currentPdf
     */
    public function setCurrentPdf($currentPdf)
    {
        $this->currentPdf = $currentPdf;
    }

    /**
     * Get last pdfSources
     *
     * @return Pdf
     */
    public function getLastPdfSource()
    {
        return $this->pdfSources->last();
    }

    /**
     * Get last version
     *
     * @return Version
     */
    public function getLastVersioin()
    {
        return $this->versions->last();
    }

    /**
     * @return ArchiveParam
     */
    public function hydrateNewArchive()
    {
        $rst = new ArchiveParam();
        $rst->setType($this->getType());
        $rst->setContrat($this->getContrat());
        $rst->setCollectivites($this->getCollectivites());
        $rst->setAction($this->getAction());
        $rst->setIdParam($this->getIdParam());
        $rst->setLibelle($this->getLibelle());
        $rst->setOrdre($this->getOrdre());
        $rst->setPartenaires($this->getPartenaires());
        $rst->setCreatedAt(new DateTime());
        $rst->setReference($this->getReference());

        return $rst;
    }
}
