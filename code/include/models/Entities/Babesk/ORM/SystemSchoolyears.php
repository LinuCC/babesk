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
    private $attendances;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $classes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $usersShouldLendBooks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attendances = new \Doctrine\Common\Collections\ArrayCollection();
        $this->classes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->usersShouldLendBooks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return SystemSchoolyears
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
     * Add attendances
     *
     * @param \Babesk\ORM\SystemAttendance $attendances
     * @return SystemSchoolyears
     */
    public function addAttendance(\Babesk\ORM\SystemAttendance $attendances)
    {
        $this->attendances[] = $attendances;

        return $this;
    }

    /**
     * Remove attendances
     *
     * @param \Babesk\ORM\SystemAttendance $attendances
     */
    public function removeAttendance(\Babesk\ORM\SystemAttendance $attendances)
    {
        $this->attendances->removeElement($attendances);
    }

    /**
     * Get attendances
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttendances()
    {
        return $this->attendances;
    }

    /**
     * Add classes
     *
     * @param \Babesk\ORM\KuwasysClass $classes
     * @return SystemSchoolyears
     */
    public function addClass(\Babesk\ORM\KuwasysClass $classes)
    {
        $this->classes[] = $classes;

        return $this;
    }

    /**
     * Remove classes
     *
     * @param \Babesk\ORM\KuwasysClass $classes
     */
    public function removeClass(\Babesk\ORM\KuwasysClass $classes)
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

    /**
     * Add usersShouldLendBooks
     *
     * @param \Babesk\ORM\SchbasUserShouldLendBook $usersShouldLendBooks
     * @return SystemSchoolyears
     */
    public function addUsersShouldLendBook(\Babesk\ORM\SchbasUserShouldLendBook $usersShouldLendBooks)
    {
        $this->usersShouldLendBooks[] = $usersShouldLendBooks;

        return $this;
    }

    /**
     * Remove usersShouldLendBooks
     *
     * @param \Babesk\ORM\SchbasUserShouldLendBook $usersShouldLendBooks
     */
    public function removeUsersShouldLendBook(\Babesk\ORM\SchbasUserShouldLendBook $usersShouldLendBooks)
    {
        $this->usersShouldLendBooks->removeElement($usersShouldLendBooks);
    }

    /**
     * Get usersShouldLendBooks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsersShouldLendBooks()
    {
        return $this->usersShouldLendBooks;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $schbasAccounting;


    /**
     * Add schbasAccounting
     *
     * @param \Babesk\ORM\SchbasAccounting $schbasAccounting
     * @return SystemSchoolyears
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
