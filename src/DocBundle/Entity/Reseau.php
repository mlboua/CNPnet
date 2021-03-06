<?php

namespace DocBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Reseau
 *
 * @ORM\Table(name="reseau")
 * @ORM\Entity(repositoryClass="DocBundle\Repository\ReseauRepository")
 */
class Reseau
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
     * @ORM\Column(name="code", type="string", length=100, unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="DocBundle\Entity\Parametrage", cascade={"persist", "remove"}, mappedBy="reseau")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parametrages;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="DocBundle\Entity\Version", cascade={"persist", "remove"}, mappedBy="reseau")
     * @ORM\JoinColumn(nullable=false)
     */
    private $versions;



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
     * Set code
     *
     * @param string $code
     *
     * @return Reseau
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Reseau
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get parametrages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParametrages()
    {
        return $this->parametrages;
    }

    /**
     * Add parametrage
     *
     * @param \DocBundle\Entity\Parametrage $parametrage
     *
     * @return Reseau
     */
    public function addParametrage(Parametrage $parametrage)
    {
        $this->parametrages[] = $parametrage;

        return $this;
    }

    /**
     * Remove parametrage
     *
     * @param \DocBundle\Entity\Parametrage $parametrage
     */
    public function removeParametrage(Parametrage $parametrage)
    {
        $this->parametrages->removeElement($parametrage);
    }

    /**
     * Add version
     *
     * @param \DocBundle\Entity\Version $version
     *
     * @return Reseau
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
     * Constructor
     */
    public function __construct()
    {
        $this->parametrages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->versions = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
