<?php

namespace DocBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LiasseComponent
 *
 * @ORM\Table(name="liasse_component")
 * @ORM\Entity(repositoryClass="DocBundle\Repository\LiasseComponentRepository")
 */
class LiasseComponent
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
     * @ORM\Column(name="ordre", type="integer")
     */
    private $ordre;

    /**
     * @var Parametrage
     *
     * @ORM\ManyToOne(targetEntity="DocBundle\Entity\Parametrage", inversedBy="liasseComponents", )
     */
    private $liasse;

    /**
     * @var Parametrage
     *
     * @ORM\ManyToOne(targetEntity="DocBundle\Entity\Parametrage")
     */
    private $cible;


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
     * Set ordre
     *
     * @param integer $ordre
     *
     * @return LiasseComponent
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return int
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set liasse
     *
     * @param \DocBundle\Entity\Parametrage $liasse
     *
     * @return LiasseComponent
     */
    public function setLiasse(\DocBundle\Entity\Parametrage $liasse = null)
    {
        $this->liasse = $liasse;

        return $this;
    }

    /**
     * Get liasse
     *
     * @return \DocBundle\Entity\Parametrage
     */
    public function getLiasse()
    {
        return $this->liasse;
    }

    /**
     * Set cible
     *
     * @param \DocBundle\Entity\Parametrage $cible
     *
     * @return Parametrage
     */
    public function setCible(\DocBundle\Entity\Parametrage $cible)
    {
        $this->cible = $cible;

        return $this;
    }

    /**
     * Get cibles
     *
     * @return Parametrage
     */
    public function getCible()
    {
        return $this->cible;
    }
}
