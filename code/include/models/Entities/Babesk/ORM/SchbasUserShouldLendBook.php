<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchbasUserShouldLendBook
 */
class SchbasUserShouldLendBook
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
     * @var \Babesk\ORM\SchbasBook
     */
    private $book;

    /**
     * @var \Babesk\ORM\SystemSchoolyears
     */
    private $schoolyear;


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
     * @return SchbasUserShouldLendBook
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
     * Set book
     *
     * @param \Babesk\ORM\SchbasBook $book
     * @return SchbasUserShouldLendBook
     */
    public function setBook(\Babesk\ORM\SchbasBook $book = null)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book
     *
     * @return \Babesk\ORM\SchbasBook 
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * Set schoolyear
     *
     * @param \Babesk\ORM\SystemSchoolyears $schoolyear
     * @return SchbasUserShouldLendBook
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
