<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * ElawaDefaultMeetingRoom
 */
class ElawaDefaultMeetingRoom
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Babesk\ORM\SystemRoom
     */
    private $room;

    /**
     * @var \Babesk\ORM\SystemUsers
     */
    private $host;

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
     * Set room
     *
     * @param \Babesk\ORM\SystemRoom $room
     * @return ElawaDefaultMeetingRoom
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

    /**
     * Set host
     *
     * @param \Babesk\ORM\SystemUsers $host
     * @return ElawaDefaultMeetingRoom
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
     * @return ElawaDefaultMeetingRoom
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
