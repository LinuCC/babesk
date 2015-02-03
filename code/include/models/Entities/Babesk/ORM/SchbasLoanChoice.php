<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchbasLoanChoice
 */
class SchbasLoanChoice
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $abbreviation;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $schbasAccounting;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->schbasAccounting = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return SchbasLoanChoice
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set name
     *
     * @param string $name
     * @return SchbasLoanChoice
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
     * Set abbreviation
     *
     * @param string $abbreviation
     * @return SchbasLoanChoice
     */
    public function setAbbreviation($abbreviation)
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    /**
     * Get abbreviation
     *
     * @return string 
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }

    /**
     * Add schbasAccounting
     *
     * @param \Babesk\ORM\SchbasAccounting $schbasAccounting
     * @return SchbasLoanChoice
     */
    public function addSchbasAccounting(\Babesk\ORM\SchbasAccounting $schbasAccounting)
    {
        $this->schbasAccounting[] = $schbasAccounting;

        return $this;
    }

    /**
     * Remove schbasAccounting
     *
     * @param \Babesk\ORM\SchbasAccounting $schbasAccounting
     */
    public function removeSchbasAccounting(\Babesk\ORM\SchbasAccounting $schbasAccounting)
    {
        $this->schbasAccounting->removeElement($schbasAccounting);
    }

    /**
     * Get schbasAccounting
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSchbasAccounting()
    {
        return $this->schbasAccounting;
    }
}
