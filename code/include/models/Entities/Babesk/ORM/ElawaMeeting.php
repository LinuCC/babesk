<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * ElawaMeeting
 */
class ElawaMeeting
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
     * @var boolean
     */
    private $isDisabled;

    /**
     * @var \Babesk\ORM\SystemUsers
     */
    private $visitor;

    /**
     * @var \Babesk\ORM\SystemUsers
     */
    private $host;

    /**
     * @var \Babesk\ORM\ElawaCategory
     */
    private $category;

    /**
     * @var \Babesk\ORM\SystemRoom
     */
    private $room;


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
     * @return ElawaMeeting
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
     * @return ElawaMeeting
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
     * Set isDisabled
     *
     * @param boolean $isDisabled
     * @return ElawaMeeting
     */
    public function setIsDisabled($isDisabled)
    {
        $this->isDisabled = $isDisabled;

        return $this;
    }

    /**
     * Get isDisabled
     *
     * @return boolean 
     */
    public function getIsDisabled()
    {
        return $this->isDisabled;
    }

    /**
     * Set visitor
     *
     * @param \Babesk\ORM\SystemUsers $visitor
     * @return ElawaMeeting
     */
    public function setVisitor(\Babesk\ORM\SystemUsers $visitor = null)
    {
        $this->visitor = $visitor;

        return $this;
    }

    /**
     * Get visitor
     *
     * @return \Babesk\ORM\SystemUsers 
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * Set host
     *
     * @param \Babesk\ORM\SystemUsers $host
     * @return ElawaMeeting
     */
    public function setHost(\Babesk\ORM\SystemUsers $host = null)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host
     *
     * @return \Babesk\ORM\SystemUsers 
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set category
     *
     * @param \Babesk\ORM\ElawaCategory $category
     * @return ElawaMeeting
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

    /**
     * Set room
     *
     * @param \Babesk\ORM\SystemRoom $room
     * @return ElawaMeeting
     */
    public function setRoom(\Babesk\ORM\SystemRoom $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return \Babesk\ORM\SystemRoom 
     */
    public function getRoom()
    {
        return $this->room;
    }
}
