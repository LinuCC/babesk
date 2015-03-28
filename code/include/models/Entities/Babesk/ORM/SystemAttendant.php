<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SystemAttendant
 */
class SystemAttendant
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Babesk\ORM\SystemUsers
     */
    private $user;

    /**
     * @var \Babesk\ORM\SystemGrades
     */
    private $grade;

    /**
     * @var \Babesk\ORM\SystemSchoolyears
     */
    private $schoolyear;


    /**
     * Set id
     *
     * @param integer $id
     * @return SystemAttendant
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
     * Set user
     *
     * @param \Babesk\ORM\SystemUsers $user
     * @return SystemAttendant
     */
    public function setUser(\Babesk\ORM\SystemUsers $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Babesk\ORM\SystemUsers 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set grade
     *
     * @param \Babesk\ORM\SystemGrades $grade
     * @return SystemAttendant
     */
    public function setGrade(\Babesk\ORM\SystemGrades $grade = null)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade
     *
     * @return \Babesk\ORM\SystemGrades 
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Set schoolyear
     *
     * @param \Babesk\ORM\SystemSchoolyears $schoolyear
     * @return SystemAttendant
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
}
