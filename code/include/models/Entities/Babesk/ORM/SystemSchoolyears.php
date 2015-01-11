<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SystemSchoolyears
 */
class SystemSchoolyears
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
     * @var boolean
     */
    private $active;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $usersInGradesAndSchoolyears;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->usersInGradesAndSchoolyears = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return SystemSchoolyears
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
     * Set active
     *
     * @param boolean $active
     * @return SystemSchoolyears
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Add usersInGradesAndSchoolyears
     *
     * @param \Babesk\ORM\SystemUsersInGradesAndSchoolyears $usersInGradesAndSchoolyears
     * @return SystemSchoolyears
     */
    public function addUsersInGradesAndSchoolyear(\Babesk\ORM\SystemUsersInGradesAndSchoolyears $usersInGradesAndSchoolyears)
    {
        $this->usersInGradesAndSchoolyears[] = $usersInGradesAndSchoolyears;

        return $this;
    }

    /**
     * Remove usersInGradesAndSchoolyears
     *
     * @param \Babesk\ORM\SystemUsersInGradesAndSchoolyears $usersInGradesAndSchoolyears
     */
    public function removeUsersInGradesAndSchoolyear(\Babesk\ORM\SystemUsersInGradesAndSchoolyears $usersInGradesAndSchoolyears)
    {
        $this->usersInGradesAndSchoolyears->removeElement($usersInGradesAndSchoolyears);
    }

    /**
     * Get usersInGradesAndSchoolyears
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsersInGradesAndSchoolyears()
    {
        return $this->usersInGradesAndSchoolyears;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $classes;


    /**
     * Add classes
     *
     * @param \Babesk\ORM\KuwasysClasses $classes
     * @return SystemSchoolyears
     */
    public function addClass(\Babesk\ORM\KuwasysClasses $classes)
    {
        $this->classes[] = $classes;

        return $this;
    }

    /**
     * Remove classes
     *
     * @param \Babesk\ORM\KuwasysClasses $classes
     */
    public function removeClass(\Babesk\ORM\KuwasysClasses $classes)
    {
        $this->classes->removeElement($classes);
    }

    /**
     * Get classes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClasses()
    {
        return $this->classes;
    }
}
