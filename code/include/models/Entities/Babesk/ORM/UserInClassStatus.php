<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserInClassStatus
 */
class UserInClassStatus
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
    private $translatedName;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $usersInClassesAndCategories;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->usersInClassesAndCategories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return UserInClassStatus
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
     * Set translatedName
     *
     * @param string $translatedName
     * @return UserInClassStatus
     */
    public function setTranslatedName($translatedName)
    {
        $this->translatedName = $translatedName;

        return $this;
    }

    /**
     * Get translatedName
     *
     * @return string 
     */
    public function getTranslatedName()
    {
        return $this->translatedName;
    }

    /**
     * Add usersInClassesAndCategories
     *
     * @param \Babesk\ORM\UserInClassAndCategory $usersInClassesAndCategories
     * @return UserInClassStatus
     */
    public function addUsersInClassesAndCategory(\Babesk\ORM\UserInClassAndCategory $usersInClassesAndCategories)
    {
        $this->usersInClassesAndCategories[] = $usersInClassesAndCategories;

        return $this;
    }

    /**
     * Remove usersInClassesAndCategories
     *
     * @param \Babesk\ORM\UserInClassAndCategory $usersInClassesAndCategories
     */
    public function removeUsersInClassesAndCategory(\Babesk\ORM\UserInClassAndCategory $usersInClassesAndCategories)
    {
        $this->usersInClassesAndCategories->removeElement($usersInClassesAndCategories);
    }

    /**
     * Get usersInClassesAndCategories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsersInClassesAndCategories()
    {
        return $this->usersInClassesAndCategories;
    }
}
