<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SystemGrades
 */
class SystemGrades
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
     * @var integer
     */
    private $gradelevel;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $usersInGradesAndSchoolyears;

    /**
     * @var \Babesk\ORM\SystemSchooltypes
     */
    private $schooltype;

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
     * @return SystemGrades
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
     * Set gradelevel
     *
     * @param integer $gradelevel
     * @return SystemGrades
     */
    public function setGradelevel($gradelevel)
    {
        $this->gradelevel = $gradelevel;

        return $this;
    }

    /**
     * Get gradelevel
     *
     * @return integer 
     */
    public function getGradelevel()
    {
        return $this->gradelevel;
    }

    /**
     * Add usersInGradesAndSchoolyears
     *
     * @param \Babesk\ORM\SystemUsersInGradesAndSchoolyears $usersInGradesAndSchoolyears
     * @return SystemGrades
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
     * Set schooltype
     *
     * @param \Babesk\ORM\SystemSchooltypes $schooltype
     * @return SystemGrades
     */
    public function setSchooltype(\Babesk\ORM\SystemSchooltypes $schooltype = null)
    {
        $this->schooltype = $schooltype;

        return $this;
    }

    /**
     * Get schooltype
     *
     * @return \Babesk\ORM\SystemSchooltypes 
     */
    public function getSchooltype()
    {
        return $this->schooltype;
    }
}
