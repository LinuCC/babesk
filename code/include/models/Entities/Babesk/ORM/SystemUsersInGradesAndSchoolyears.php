<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SystemUsersInGradesAndSchoolyears
 */
class SystemUsersInGradesAndSchoolyears
{
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
     * Set user
     *
     * @param \Babesk\ORM\SystemUsers $user
     * @return SystemUsersInGradesAndSchoolyears
     */
    public function setUser(\Babesk\ORM\SystemUsers $user)
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
     * @return SystemUsersInGradesAndSchoolyears
     */
    public function setGrade(\Babesk\ORM\SystemGrades $grade)
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
     * @return SystemUsersInGradesAndSchoolyears
     */
    public function setSchoolyear(\Babesk\ORM\SystemSchoolyears $schoolyear)
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
