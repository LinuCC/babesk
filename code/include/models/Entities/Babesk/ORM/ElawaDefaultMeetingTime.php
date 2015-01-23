<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * ElawaDefaultMeetingTime
 */
class ElawaDefaultMeetingTime
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $time;

    /**
     * @var \DateTime
     */
    private $length;

    /**
     * @var \Babesk\ORM\ElawaCategory
     */
    private $category;


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
     * Set time
     *
     * @param \DateTime $time
     * @return ElawaDefaultMeetingTime
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set length
     *
     * @param \DateTime $length
     * @return ElawaDefaultMeetingTime
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get length
     *
     * @return \DateTime 
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set category
     *
     * @param \Babesk\ORM\ElawaCategory $category
     * @return ElawaDefaultMeetingTime
     */
    public function setCategory(\Babesk\ORM\ElawaCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Babesk\ORM\ElawaCategory 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
