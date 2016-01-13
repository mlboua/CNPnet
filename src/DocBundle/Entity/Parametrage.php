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
     * @ORM\OneToOne(targetEntity="DocBundle\Entity\Pdf", cascade={"persist"})
     */
    private $pdfSource;


    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", nullable=true)
     */
    private $commentaire;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="updateAt", type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;

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
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Parametrage
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set updateAt
     *
     * @param DateTime $updateAt
     *
     * @return Parametrage
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * Get updateAt
     *
     * @return DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * Set createdAt
     *
     * @param DateTime $createdAt
     *
     * @return Parametrage
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set pdfSource
     *
     *
     * @return Parametrage
     */
    public function setPdfSource($pdfSource)
    {
        $this->pdfSource = $pdfSource;

        return $this;
    }

    /**
     * Get pdfSource
     *
     * @return Pdf
     */
    public function getPdfSource()
    {
        return $this->pdfSource;
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
}
