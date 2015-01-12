<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class
 */
class Class
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $description;

    /**
     * @var integer
     */
    private $maxRegistration;

    /**
     * @var boolean
     */
    private $registrationEnabled;

    /**
     * @var boolean
     */
    private $isOptional;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $usersInClassesAndCategories;

    /**
     * @var \Babesk\ORM\SystemSchoolyears
     */
    private $schoolyear;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $categories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $classteachers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->usersInClassesAndCategories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->classteachers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set label
     *
     * @param string $label
     * @return Class
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Class
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set maxRegistration
     *
     * @param integer $maxRegistration
     * @return Class
     */
    public function setMaxRegistration($maxRegistration)
    {
        $this->maxRegistration = $maxRegistration;

        return $this;
    }

    /**
     * Get maxRegistration
     *
     * @return integer 
     */
    public function getMaxRegistration()
    {
        return $this->maxRegistration;
    }

    /**
     * Set registrationEnabled
     *
     * @param boolean $registrationEnabled
     * @return Class
     */
    public function setRegistrationEnabled($registrationEnabled)
    {
        $this->registrationEnabled = $registrationEnabled;

        return $this;
    }

    /**
     * Get registrationEnabled
     *
     * @return boolean 
     */
    public function getRegistrationEnabled()
    {
        return $this->registrationEnabled;
    }

    /**
     * Set isOptional
     *
     * @param boolean $isOptional
     * @return Class
     */
    public function setIsOptional($isOptional)
    {
        $this->isOptional = $isOptional;

        return $this;
    }

    /**
     * Get isOptional
     *
     * @return boolean 
     */
    public function getIsOptional()
    {
        return $this->isOptional;
    }

    /**
     * Add usersInClassesAndCategories
     *
     * @param \Babesk\ORM\UserInClassAndCategory $usersInClassesAndCategories
     * @return Class
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

    /**
     * Set schoolyear
     *
     * @param \Babesk\ORM\SystemSchoolyears $schoolyear
     * @return Class
     */
    public function setSchoolyear(\Babesk\ORM\SystemSchoolyears $schoolyear = null)
    {
        $this->schoolyear = $schoolyear;

        return $this;
    }

    /**
     * Get schoolyear
     *
     * @return \Babesk\ORM\SystemSchoolyears 
     */
    public function getSchoolyear()
    {
        return $this->schoolyear;
    }

    /**
     * Add categories
     *
     * @param \Babesk\ORM\KuwasysClassCategories $categories
     * @return Class
     */
    public function addCategory(\Babesk\ORM\KuwasysClassCategories $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Babesk\ORM\KuwasysClassCategories $categories
     */
    public function removeCategory(\Babesk\ORM\KuwasysClassCategories $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add classteachers
     *
     * @param \Babesk\ORM\Classteacher $classteachers
     * @return Class
     */
    public function addClassteacher(\Babesk\ORM\Classteacher $classteachers)
    {
        $this->classteachers[] = $classteachers;

        return $this;
    }

    /**
     * Remove classteachers
     *
     * @param \Babesk\ORM\Classteacher $classteachers
     */
    public function removeClassteacher(\Babesk\ORM\Classteacher $classteachers)
    {
        $this->classteachers->removeElement($classteachers);
    }

    /**
     * Get classteachers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClassteachers()
    {
        return $this->classteachers;
    }
}
