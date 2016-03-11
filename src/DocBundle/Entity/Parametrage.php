<?php

namespace DocBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CnpDocument
 * 
 * @ORM\Table(name="parametrage")
 * @ORM\Entity(repositoryClass="DocBundle\Repository\ParametrageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Parametrage
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
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="DocBundle\Entity\Reseau", inversedBy="parametrages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $reseau;

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
     * @var Pdf
     *
     * @ORM\OneToMany(targetEntity="DocBundle\Entity\Pdf", cascade={"persist"}, mappedBy="parametrage")
     */
    private $pdfSources;

    /**
     * @var Pdf
     *
     */
    private $currentPdf;


    /**
     * @var string
     *
     * @ORM\Column(name="deleting", type="boolean", nullable=true)
     */
    private $deleting;

    /**
     * @var string
     *
     * @ORM\Column(name="editing", type="boolean", nullable=true)
     */
    private $editing;

    /**
     * @var string
     *
     * @ORM\Column(name="liasse", type="boolean", nullable=true)
     */
    private $liasse;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pdfSources = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add pdfSource
     *
     * @param \DocBundle\Entity\Pdf $pdfSource
     *
     * @return Parametrage
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
     * Get pdfSources
     *
     * @return Pdf
     */
    public function getLastPdfSource()
    {
        return $this->pdfSources->last();
    }

    /**
     * @return Pdf
     */
    public function getCurrentPdf()
    {
        return $this->currentPdf;
    }

    /**
     * @param Pdf $currentPdf
     */
    public function setCurrentPdf($currentPdf)
    {
        $this->currentPdf = $currentPdf;
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
     * Set collectivites
     *
     * @param string $collectivites
     *
     * @return Parametrage
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
     * @return Parametrage
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
     * @return Parametrage
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
     * @return Parametrage
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
     * @return Parametrage
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
     * @return Parametrage
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
     * Set deleting
     *
     * @param string $deleting
     *
     * @return Parametrage
     */
    public function setDeleting($deleting)
    {
        $this->deleting = $deleting;

        return $this;
    }

    /**
     * Get deleting
     *
     * @return string
     */
    public function getDeleting()
    {
        return $this->deleting;
    }

    /**
     * Set reseau
     *
     * @param \DocBundle\Entity\Reseau $reseau
     *
     * @return Parametrage
     */
    public function setReseau(\DocBundle\Entity\Reseau $reseau)
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
     * Set partenaires
     *
     * @param string $partenaires
     *
     * @return Parametrage
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
     * Set editing
     *
     * @param boolean $editing
     *
     * @return Parametrage
     */
    public function setEditing($editing)
    {
        $this->editing = $editing;

        return $this;
    }

    /**
     * Get editing
     *
     * @return boolean
     */
    public function getEditing()
    {
        return $this->editing;
    }

    /**
     * @return string
     */
    public function isLiasse()
    {
        return $this->liasse;
    }

    /**
     * @param string $liasse
     */
    public function setLiasse($liasse)
    {
        $this->liasse = $liasse;
    }


}
