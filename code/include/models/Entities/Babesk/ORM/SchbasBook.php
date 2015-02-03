<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchbasBook
 */
class SchbasBook
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $publisher;

    /**
     * @var string
     */
    private $isbn;

    /**
     * @var string
     */
    private $class;

    /**
     * @var integer
     */
    private $bundle;

    /**
     * @var float
     */
    private $price;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $exemplars;

    /**
     * @var \Babesk\ORM\SystemSchoolSubject
     */
    private $subject;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $selfpayingUsers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->exemplars = new \Doctrine\Common\Collections\ArrayCollection();
        $this->selfpayingUsers = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return SchbasBook
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
     * Set author
     *
     * @param string $author
     * @return SchbasBook
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set publisher
     *
     * @param string $publisher
     * @return SchbasBook
     */
    public function setPublisher($publisher)
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * Get publisher
     *
     * @return string 
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * Set isbn
     *
     * @param string $isbn
     * @return SchbasBook
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * Get isbn
     *
     * @return string 
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * Set class
     *
     * @param string $class
     * @return SchbasBook
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set bundle
     *
     * @param integer $bundle
     * @return SchbasBook
     */
    public function setBundle($bundle)
    {
        $this->bundle = $bundle;

        return $this;
    }

    /**
     * Get bundle
     *
     * @return integer 
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return SchbasBook
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Add exemplars
     *
     * @param \Babesk\ORM\SchbasInventory $exemplars
     * @return SchbasBook
     */
    public function addExemplar(\Babesk\ORM\SchbasInventory $exemplars)
    {
        $this->exemplars[] = $exemplars;

        return $this;
    }

    /**
     * Remove exemplars
     *
     * @param \Babesk\ORM\SchbasInventory $exemplars
     */
    public function removeExemplar(\Babesk\ORM\SchbasInventory $exemplars)
    {
        $this->exemplars->removeElement($exemplars);
    }

    /**
     * Get exemplars
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExemplars()
    {
        return $this->exemplars;
    }

    /**
     * Set subject
     *
     * @param \Babesk\ORM\SystemSchoolSubject $subject
     * @return SchbasBook
     */
    public function setSubject(\Babesk\ORM\SystemSchoolSubject $subject = null)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return \Babesk\ORM\SystemSchoolSubject 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Add selfpayingUsers
     *
     * @param \Babesk\ORM\SystemUsers $selfpayingUsers
     * @return SchbasBook
     */
    public function addSelfpayingUser(\Babesk\ORM\SystemUsers $selfpayingUsers)
    {
        $this->selfpayingUsers[] = $selfpayingUsers;

        return $this;
    }

    /**
     * Remove selfpayingUsers
     *
     * @param \Babesk\ORM\SystemUsers $selfpayingUsers
     */
    public function removeSelfpayingUser(\Babesk\ORM\SystemUsers $selfpayingUsers)
    {
        $this->selfpayingUsers->removeElement($selfpayingUsers);
    }

    /**
     * Get selfpayingUsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSelfpayingUsers()
    {
        return $this->selfpayingUsers;
    }
}
