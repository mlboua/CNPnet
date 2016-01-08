<?php

namespace DocBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Pdf
 *
 * @ORM\Table(name="pdf")
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
     * @var
     */
    private $file;

    /**
     * @var
     */
    private $tempFilename;


    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        /*if (file_exists($this->title)) {
            return
        }*/
        //TODO: Check that the file doesn't exist
        if (null !== $this->getFile()) {
            //$filename = sha1(uniqid(mt_rand(), true));
            //$this->title = $filename.'.'.$this->getFile()->guessExtension();
            //$this->title
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }
        if (isset($this->tempFilename)) {
            $oldFile = $this->getUploadRootDir().'/'.$this->tempFilename;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            $this->tempFilename = null;
        }
        $this->getFile()->move($this->getUploadRootDir(),$this->title);
        $this->file = null;
    }

    /**
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    /**
     * @return string
     */
    public function getUploadDir()
    {
        return 'uploads/pdf';
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $this->tempFilename = $this->getUploadRootDir().'/'.$this->title;
        if (file_exists($this->tempFilename)) {
            unlink($this->tempFilename);
        }
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
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
        if (isset($this->title)) {
            $this->tempFilename = $this->title;
            $this->title = null;
        }
    }

    /**
     * @return mixed
     */
    public function getTempFilename()
    {
        return $this->tempFilename;
    }

    /**
     * @param mixed $tempFilename
     */
    public function setTempFilename($tempFilename)
    {
        $this->tempFilename = $tempFilename;
    }


}
