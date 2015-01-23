<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * ElawaCategory
 */
class ElawaCategory
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $meetings;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $defaultMeetingTimes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $defaultMeetingRooms;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->meetings = new \Doctrine\Common\Collections\ArrayCollection();
        $this->defaultMeetingTimes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->defaultMeetingRooms = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return ElawaCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add meetings
     *
     * @param \Babesk\ORM\ElawaMeeting $meetings
     * @return ElawaCategory
     */
    public function addMeeting(\Babesk\ORM\ElawaMeeting $meetings)
    {
        $this->meetings[] = $meetings;

        return $this;
    }

    /**
     * Remove meetings
     *
     * @param \Babesk\ORM\ElawaMeeting $meetings
     */
    public function removeMeeting(\Babesk\ORM\ElawaMeeting $meetings)
    {
        $this->meetings->removeElement($meetings);
    }

    /**
     * Get meetings
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMeetings()
    {
        return $this->meetings;
    }

    /**
     * Add defaultMeetingTimes
     *
     * @param \Babesk\ORM\ElawaDefaultMeetingTime $defaultMeetingTimes
     * @return ElawaCategory
     */
    public function addDefaultMeetingTime(\Babesk\ORM\ElawaDefaultMeetingTime $defaultMeetingTimes)
    {
        $this->defaultMeetingTimes[] = $defaultMeetingTimes;

        return $this;
    }

    /**
     * Remove defaultMeetingTimes
     *
     * @param \Babesk\ORM\ElawaDefaultMeetingTime $defaultMeetingTimes
     */
    public function removeDefaultMeetingTime(\Babesk\ORM\ElawaDefaultMeetingTime $defaultMeetingTimes)
    {
        $this->defaultMeetingTimes->removeElement($defaultMeetingTimes);
    }

    /**
     * Get defaultMeetingTimes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDefaultMeetingTimes()
    {
        return $this->defaultMeetingTimes;
    }

    /**
     * Add defaultMeetingRooms
     *
     * @param \Babesk\ORM\ElawaDefaultMeetingRoom $defaultMeetingRooms
     * @return ElawaCategory
     */
    public function addDefaultMeetingRoom(\Babesk\ORM\ElawaDefaultMeetingRoom $defaultMeetingRooms)
    {
        $this->defaultMeetingRooms[] = $defaultMeetingRooms;

        return $this;
    }

    /**
     * Remove defaultMeetingRooms
     *
     * @param \Babesk\ORM\ElawaDefaultMeetingRoom $defaultMeetingRooms
     */
    public function removeDefaultMeetingRoom(\Babesk\ORM\ElawaDefaultMeetingRoom $defaultMeetingRooms)
    {
        $this->defaultMeetingRooms->removeElement($defaultMeetingRooms);
    }

    /**
     * Get defaultMeetingRooms
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDefaultMeetingRooms()
    {
        return $this->defaultMeetingRooms;
    }
}
