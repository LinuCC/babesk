<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchbasInventory
 */
class SchbasInventory
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $yearOfPurchase;

    /**
     * @var integer
     */
    private $exemplar;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lending;

    /**
     * @var \Babesk\ORM\SchbasBooks
     */
    private $book;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $usersLent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lending = new \Doctrine\Common\Collections\ArrayCollection();
        $this->usersLent = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set yearOfPurchase
     *
     * @param integer $yearOfPurchase
     * @return SchbasInventory
     */
    public function setYearOfPurchase($yearOfPurchase)
    {
        $this->yearOfPurchase = $yearOfPurchase;

        return $this;
    }

    /**
     * Get yearOfPurchase
     *
     * @return integer 
     */
    public function getYearOfPurchase()
    {
        return $this->yearOfPurchase;
    }

    /**
     * Set exemplar
     *
     * @param integer $exemplar
     * @return SchbasInventory
     */
    public function setExemplar($exemplar)
    {
        $this->exemplar = $exemplar;

        return $this;
    }

    /**
     * Get exemplar
     *
     * @return integer 
     */
    public function getExemplar()
    {
        return $this->exemplar;
    }

    /**
     * Add lending
     *
     * @param \Babesk\ORM\SchbasLending $lending
     * @return SchbasInventory
     */
    public function addLending(\Babesk\ORM\SchbasLending $lending)
    {
        $this->lending[] = $lending;

        return $this;
    }

    /**
     * Remove lending
     *
     * @param \Babesk\ORM\SchbasLending $lending
     */
    public function removeLending(\Babesk\ORM\SchbasLending $lending)
    {
        $this->lending->removeElement($lending);
    }

    /**
     * Get lending
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLending()
    {
        return $this->lending;
    }

    /**
     * Set book
     *
     * @param \Babesk\ORM\SchbasBooks $book
     * @return SchbasInventory
     */
    public function setBook(\Babesk\ORM\SchbasBooks $book = null)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book
     *
     * @return \Babesk\ORM\SchbasBooks 
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * Add usersLent
     *
     * @param \Babesk\ORM\SystemUsers $usersLent
     * @return SchbasInventory
     */
    public function addUsersLent(\Babesk\ORM\SystemUsers $usersLent)
    {
        $this->usersLent[] = $usersLent;

        return $this;
    }

    /**
     * Remove usersLent
     *
     * @param \Babesk\ORM\SystemUsers $usersLent
     */
    public function removeUsersLent(\Babesk\ORM\SystemUsers $usersLent)
    {
        $this->usersLent->removeElement($usersLent);
    }

    /**
     * Get usersLent
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsersLent()
    {
        return $this->usersLent;
    }
}
