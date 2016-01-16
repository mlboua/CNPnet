<?php

namespace DocBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArchivePdf
 *
 * @ORM\Table(name="archive_pdf")
 * @ORM\Entity(repositoryClass="DocBundle\Repository\ArchivePdfRepository")
 */
class ArchivePdf extends Pdf
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
