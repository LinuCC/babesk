<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchbasSelfpayer
 */
class SchbasSelfpayer
{

    /**
     * @var \Babesk\ORM\SchbasBook
     */
    private $book;

    /**
     * @var \Babesk\ORM\SystemUsers
     */
    private $user;


    /**
     * Set book
     *
     * @param \Babesk\ORM\SchbasBook $book
     * @return SchbasSelfpayer
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
     * Set user
     *
     * @param \Babesk\ORM\SystemUsers $user
     * @return SchbasSelfpayer
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
     * @var integer
     */
    private $BID;


    /**
     * Set BID
     *
     * @param integer $bID
     * @return SchbasSelfpayer
     */
    public function setBID($bID)
    {
        $this->BID = $bID;

        return $this;
    }

    /**
     * Get BID
     *
     * @return integer 
     */
    public function getBID()
    {
        return $this->BID;
    }
}
