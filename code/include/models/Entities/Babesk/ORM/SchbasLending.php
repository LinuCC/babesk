<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchbasLending
 */
class SchbasLending
{
    /**
     * @var \DateTime
     */
    private $lendDate;

    /**
     * @var \Babesk\ORM\SystemUsers
     */
    private $user;

    /**
     * @var \Babesk\ORM\SchbasInventory
     */
    private $inventory;


    /**
     * Set lendDate
     *
     * @param \DateTime $lendDate
     * @return SchbasLending
     */
    public function setLendDate($lendDate)
    {
        $this->lendDate = $lendDate;

        return $this;
    }

    /**
     * Get lendDate
     *
     * @return \DateTime 
     */
    public function getLendDate()
    {
        return $this->lendDate;
    }

    /**
     * Set user
     *
     * @param \Babesk\ORM\SystemUsers $user
     * @return SchbasLending
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
     * Set inventory
     *
     * @param \Babesk\ORM\SchbasInventory $inventory
     * @return SchbasLending
     */
    public function setInventory(\Babesk\ORM\SchbasInventory $inventory = null)
    {
        $this->inventory = $inventory;

        return $this;
    }

    /**
     * Get inventory
     *
     * @return \Babesk\ORM\SchbasInventory 
     */
    public function getInventory()
    {
        return $this->inventory;
    }
}
