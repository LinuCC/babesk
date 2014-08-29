<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * BabeskPriceGroups
 */
class BabeskPriceGroups
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
    private $max_credit;


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
     * @return BabeskPriceGroups
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
     * Set max_credit
     *
     * @param string $maxCredit
     * @return BabeskPriceGroups
     */
    public function setMaxCredit($maxCredit)
    {
        $this->max_credit = $maxCredit;

        return $this;
    }

    /**
     * Get max_credit
     *
     * @return string 
     */
    public function getMaxCredit()
    {
        return $this->max_credit;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add users
     *
     * @param \Babesk\ORM\SystemUsers $users
     * @return BabeskPriceGroups
     */
    public function addUser(\Babesk\ORM\SystemUsers $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Babesk\ORM\SystemUsers $users
     */
    public function removeUser(\Babesk\ORM\SystemUsers $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
}
