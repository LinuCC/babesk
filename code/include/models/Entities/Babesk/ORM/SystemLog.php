<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SystemLog
 */
class SystemLog
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $message;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $data;

    /**
     * @var \Babesk\ORM\SystemLogSeverity
     */
    private $severity;

    /**
     * @var \Babesk\ORM\SystemLogCategory
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
     * Set message
     *
     * @param string $message
     * @return SystemLog
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return SystemLog
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return SystemLog
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set severity
     *
     * @param \Babesk\ORM\SystemLogSeverity $severity
     * @return SystemLog
     */
    public function setSeverity(\Babesk\ORM\SystemLogSeverity $severity = null)
    {
        $this->severity = $severity;

        return $this;
    }

    /**
     * Get severity
     *
     * @return \Babesk\ORM\SystemLogSeverity 
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * Set category
     *
     * @param \Babesk\ORM\SystemLogCategory $category
     * @return SystemLog
     */
    public function setCategory(\Babesk\ORM\SystemLogCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Babesk\ORM\SystemLogCategory 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
