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
    private $attendants;

    /**
     * @var \Babesk\ORM\SystemSchooltypes
     */
    private $schooltype;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attendants = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add attendants
     *
     * @param \Babesk\ORM\SystemAttendant $attendants
     * @return SystemGrades
     */
    public function addAttendant(\Babesk\ORM\SystemAttendant $attendants)
    {
        $this->attendants[] = $attendants;

        return $this;
    }

    /**
     * Remove attendants
     *
     * @param \Babesk\ORM\SystemAttendant $attendants
     */
    public function removeAttendant(\Babesk\ORM\SystemAttendant $attendants)
    {
        $this->attendants->removeElement($attendants);
    }

    /**
     * Get attendants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttendants()
    {
        return $this->attendants;
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
