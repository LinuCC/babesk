<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * KuwasysClasses
 */
class KuwasysClasses
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
     * @var string
     */
    private $manyToOne;


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
     * @return KuwasysClasses
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
     * @return KuwasysClasses
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
     * @return KuwasysClasses
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
     * @return KuwasysClasses
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
     * @return KuwasysClasses
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
     * Set manyToOne
     *
     * @param string $manyToOne
     * @return KuwasysClasses
     */
    public function setManyToOne($manyToOne)
    {
        $this->manyToOne = $manyToOne;

        return $this;
    }

    /**
     * Get manyToOne
     *
     * @return string 
     */
    public function getManyToOne()
    {
        return $this->manyToOne;
    }
    /**
     * @var \Babesk\ORM\SystemSchoolyears
     */
    private $schoolyear;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $categories;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set schoolyear
     *
     * @param \Babesk\ORM\SystemSchoolyears $schoolyear
     * @return KuwasysClasses
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
     * @return KuwasysClasses
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $classteachers;


    /**
     * Add classteachers
     *
     * @param \Babesk\ORM\KuwasysClassteachers $classteachers
     * @return KuwasysClasses
     */
    public function addClassteacher(\Babesk\ORM\KuwasysClassteachers $classteachers)
    {
        $this->classteachers[] = $classteachers;

        return $this;
    }

    /**
     * Remove classteachers
     *
     * @param \Babesk\ORM\KuwasysClassteachers $classteachers
     */
    public function removeClassteacher(\Babesk\ORM\KuwasysClassteachers $classteachers)
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
