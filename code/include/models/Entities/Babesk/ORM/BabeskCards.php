<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * BabeskCards
 */
class BabeskCards
{
    /**
     * @var integer
     */
    private $ID;

    /**
     * @var string
     */
    private $cardnumber;

    /**
     * @var integer
     */
    private $UID;

    /**
     * @var integer
     */
    private $changed_cardID;

    /**
     * @var boolean
     */
    private $lost;


    /**
     * Get ID
     *
     * @return integer 
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * Set cardnumber
     *
     * @param string $cardnumber
     * @return BabeskCards
     */
    public function setCardnumber($cardnumber)
    {
        $this->cardnumber = $cardnumber;

        return $this;
    }

    /**
     * Get cardnumber
     *
     * @return string 
     */
    public function getCardnumber()
    {
        return $this->cardnumber;
    }

    /**
     * Set UID
     *
     * @param integer $uID
     * @return BabeskCards
     */
    public function setUID($uID)
    {
        $this->UID = $uID;

        return $this;
    }

    /**
     * Get UID
     *
     * @return integer 
     */
    public function getUID()
    {
        return $this->UID;
    }

    /**
     * Set changed_cardID
     *
     * @param integer $changedCardID
     * @return BabeskCards
     */
    public function setChangedCardID($changedCardID)
    {
        $this->changed_cardID = $changedCardID;

        return $this;
    }

    /**
     * Get changed_cardID
     *
     * @return integer 
     */
    public function getChangedCardID()
    {
        return $this->changed_cardID;
    }

    /**
     * Set lost
     *
     * @param boolean $lost
     * @return BabeskCards
     */
    public function setLost($lost)
    {
        $this->lost = $lost;

        return $this;
    }

    /**
     * Get lost
     *
     * @return boolean 
     */
    public function getLost()
    {
        return $this->lost;
    }
    /**
     * @var \Babesk\ORM\SystemUsers
     */
    private $user;


    /**
     * Set user
     *
     * @param \Babesk\ORM\SystemUsers $user
     * @return BabeskCards
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
}
