<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchbasAccounting
 */
class SchbasAccounting
{
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
     * @var \Babesk\ORM\SchbasLoanChoices
     */
    private $loanChoice;


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
    public function setUser(\Babesk\ORM\SystemUsers $user)
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
