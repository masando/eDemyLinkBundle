<?php

namespace eDemy\LinkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use eDemy\MainBundle\Entity\BaseEntity;

/**
 * @ORM\Entity(repositoryClass="eDemy\LinkBundle\Entity\LinkRepository")
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks
 */
class Link extends BaseEntity
{
    public $temp;

    public function __construct($em = null)
    {
        parent::__construct($em);
    }

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function showTitleInPanel()
    {
        return true;
    }

    public function showTitleInForm()
    {
        return true;
    }
    
    /**
     * @ORM\Column(name="url", type="string", length=1024)
     */
    protected $url;

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function showUrlInForm()
    {
        return true;
    }

    /**
     * @ORM\Column(name="abstract", type="text")
     */
    protected $abstract;

    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;

        return $this;
    }

    public function getAbstract()
    {
        return $this->abstract;
    }
    
    public function showAbstractInForm()
    {
        return true;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    public function getPath()
    {
        return $this->path;
    }

    public function getAbsolutePath($host = null)
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir($this->getHost()).'/'.$this->path;
    }

    public function getHost()
    {
        $host = $_SERVER['HTTP_HOST'];
        $domain = $host;
        $parts = explode(".", $host);
        if (count($parts) == 3) {
            $subdomain = $parts[0];
            $domain = $parts[1] . '.' . $parts[2];
        } else {
            $domain = $parts[0] . '.' . $parts[1];
            $subdomain = 'www';
        }

        return $domain;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir($this->getHost()).'/'.$this->path;
    }

    protected function getUploadRootDir($host = null)
    {
        if($host) {
            $basedir = '/var/www/'.$host;
        } else {
            if(strpos(__DIR__, 'app/cache/')) {
                // subimos hasta el directorio raíz de la aplicación (3 niveles)
                $basedir = __DIR__ . '/../../../web';
            } else {
                // si no subimos 6 niveles hasta el directorio raíz de la aplicación
                $basedir = __DIR__ . '/../../../../../../web';
            }
        }

        return $basedir . $this->getUploadDir($host);
    }

    protected function getUploadDir($host = null)
    {
        $host = $_SERVER['HTTP_HOST'];
        if($host) {

            return '/images';
        } else {

            return '/images';
        }
    }

    /**
     * @Assert\File(maxSize="6000000")
     */
    protected $file;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }
    
    public function showFileInForm()
    {
        return true;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename.'.'.$this->getFile()->guessExtension();
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

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir($this->getHost()), $this->path);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            if(file_exists($this->getUploadRootDir($this->getHost()).'/'.$this->temp)) {
                unlink($this->getUploadRootDir($this->getHost()).'/'.$this->temp);
            }
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }
    
    protected $webpath;
    
    public function showWebpathInForm()
    {
        return true;
    }
}
