<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchbasAccounting
 */
class SchbasAccounting
{
    /**
     * @var integer
     */
    private $UID;

    /**
     * @var string
     */
    private $payedAmount;

    /**
     * @var string
     */
    private $amountToPay;

    /**
     * @var \Babesk\ORM\SystemUsers
     */
    private $user;


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
     * Set payedAmount
     *
     * @param string $payedAmount
     * @return SchbasAccounting
     */
    public function setPayedAmount($payedAmount)
    {
        $this->payedAmount = $payedAmount;

        return $this;
    }

    /**
     * Get payedAmount
     *
     * @return string 
     */
    public function getPayedAmount()
    {
        return $this->payedAmount;
    }

    /**
     * Set amountToPay
     *
     * @param string $amountToPay
     * @return SchbasAccounting
     */
    public function setAmountToPay($amountToPay)
    {
        $this->amountToPay = $amountToPay;

        return $this;
    }

    /**
     * Get amountToPay
     *
     * @return string 
     */
    public function getAmountToPay()
    {
        return $this->amountToPay;
    }

    /**
     * Set user
     *
     * @param \Babesk\ORM\SystemUsers $user
     * @return SchbasAccounting
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
     * Set UID
     *
     * @param \Babesk\ORM\SystemUsers $uID
     * @return SchbasAccounting
     */
    public function setUID(\Babesk\ORM\SystemUsers $uID)
    {
        $this->UID = $uID;

        return $this;
    }
    /**
     * @var integer
     */
    private $id;


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
     * @var \Babesk\ORM\SchbasLoanChoices
     */
    private $loanChoice;


    /**
     * Set loanChoice
     *
     * @param \Babesk\ORM\SchbasLoanChoices $loanChoice
     * @return SchbasAccounting
     */
    public function setLoanChoice(\Babesk\ORM\SchbasLoanChoices $loanChoice = null)
    {
        $this->loanChoice = $loanChoice;

        return $this;
    }

    /**
     * Get loanChoice
     *
     * @return \Babesk\ORM\SchbasLoanChoices 
     */
    public function getLoanChoice()
    {
        return $this->loanChoice;
    }
}
