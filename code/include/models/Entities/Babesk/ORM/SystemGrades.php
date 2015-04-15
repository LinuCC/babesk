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
    private $attendances;

    /**
     * @var \Babesk\ORM\SystemSchooltypes
     */
    private $schooltype;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attendances = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return SystemGrades
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
     * Add attendances
     *
     * @param \Babesk\ORM\SystemAttendance $attendances
     * @return SystemGrades
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
