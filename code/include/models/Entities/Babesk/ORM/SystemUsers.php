<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SystemUsers
 */
class SystemUsers
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
     * @var string
     */
    private $forename;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $telephone;

    /**
     * @var string
     */
    private $birthday;

    /**
     * @var string
     */
    private $last_login;

    /**
     * @var integer
     */
    private $login_tries;

    /**
     * @var boolean
     */
    private $first_passwd;

    /**
     * @var boolean
     */
    private $locked;

    /**
     * @var float
     */
    private $credit;

    /**
     * @var boolean
     */
    private $soli;

    /**
     * @var string
     */
    private $religion;

    /**
     * @var string
     */
    private $foreign_language;

    /**
     * @var string
     */
    private $special_course;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $attendances;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $usersInClassesAndCategories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $cards;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $elawaMeetingsVisiting;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $elawaMeetingsHosting;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $elawaDefaultMeetingRooms;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $booksToLend;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $selfpayingBookEntities;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $schbasAccounting;

    /**
     * @var \Babesk\ORM\BabeskPriceGroups
     */
    private $priceGroup;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $groups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $bookLending;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $selfpayingBooks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attendances = new \Doctrine\Common\Collections\ArrayCollection();
        $this->usersInClassesAndCategories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->cards = new \Doctrine\Common\Collections\ArrayCollection();
        $this->elawaMeetingsVisiting = new \Doctrine\Common\Collections\ArrayCollection();
        $this->elawaMeetingsHosting = new \Doctrine\Common\Collections\ArrayCollection();
        $this->elawaDefaultMeetingRooms = new \Doctrine\Common\Collections\ArrayCollection();
        $this->booksToLend = new \Doctrine\Common\Collections\ArrayCollection();
        $this->selfpayingBookEntities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->schbasAccounting = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->bookLending = new \Doctrine\Common\Collections\ArrayCollection();
        $this->selfpayingBooks = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return SystemUsers
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
     * Set forename
     *
     * @param string $forename
     * @return SystemUsers
     */
    public function setForename($forename)
    {
        $this->forename = $forename;

        return $this;
    }

    /**
     * Get forename
     *
     * @return string 
     */
    public function getForename()
    {
        return $this->forename;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return SystemUsers
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return SystemUsers
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return SystemUsers
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return SystemUsers
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set birthday
     *
     * @param string $birthday
     * @return SystemUsers
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return string 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set last_login
     *
     * @param string $lastLogin
     * @return SystemUsers
     */
    public function setLastLogin($lastLogin)
    {
        $this->last_login = $lastLogin;

        return $this;
    }

    /**
     * Get last_login
     *
     * @return string 
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * Set login_tries
     *
     * @param integer $loginTries
     * @return SystemUsers
     */
    public function setLoginTries($loginTries)
    {
        $this->login_tries = $loginTries;

        return $this;
    }

    /**
     * Get login_tries
     *
     * @return integer 
     */
    public function getLoginTries()
    {
        return $this->login_tries;
    }

    /**
     * Set first_passwd
     *
     * @param boolean $firstPasswd
     * @return SystemUsers
     */
    public function setFirstPasswd($firstPasswd)
    {
        $this->first_passwd = $firstPasswd;

        return $this;
    }

    /**
     * Get first_passwd
     *
     * @return boolean 
     */
    public function getFirstPasswd()
    {
        return $this->first_passwd;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return SystemUsers
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean 
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set credit
     *
     * @param float $credit
     * @return SystemUsers
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return float 
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set soli
     *
     * @param boolean $soli
     * @return SystemUsers
     */
    public function setSoli($soli)
    {
        $this->soli = $soli;

        return $this;
    }

    /**
     * Get soli
     *
     * @return boolean 
     */
    public function getSoli()
    {
        return $this->soli;
    }

    /**
     * Set religion
     *
     * @param string $religion
     * @return SystemUsers
     */
    public function setReligion($religion)
    {
        $this->religion = $religion;

        return $this;
    }

    /**
     * Get religion
     *
     * @return string 
     */
    public function getReligion()
    {
        return $this->religion;
    }

    /**
     * Set foreign_language
     *
     * @param string $foreignLanguage
     * @return SystemUsers
     */
    public function setForeignLanguage($foreignLanguage)
    {
        $this->foreign_language = $foreignLanguage;

        return $this;
    }

    /**
     * Get foreign_language
     *
     * @return string 
     */
    public function getForeignLanguage()
    {
        return $this->foreign_language;
    }

    /**
     * Set special_course
     *
     * @param string $specialCourse
     * @return SystemUsers
     */
    public function setSpecialCourse($specialCourse)
    {
        $this->special_course = $specialCourse;

        return $this;
    }

    /**
     * Get special_course
     *
     * @return string 
     */
    public function getSpecialCourse()
    {
        return $this->special_course;
    }

    /**
     * Add attendances
     *
     * @param \Babesk\ORM\SystemAttendance $attendances
     * @return SystemUsers
     */
    public function addAttendance(\Babesk\ORM\SystemAttendance $attendances)
    {
        $this->attendances[] = $attendances;

        return $this;
    }

    /**
     * Remove attendances
     *
     * @param \Babesk\ORM\SystemAttendance $attendances
     */
    public function removeAttendance(\Babesk\ORM\SystemAttendance $attendances)
    {
        $this->attendances->removeElement($attendances);
    }

    /**
     * Get attendances
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttendances()
    {
        return $this->attendances;
    }

    /**
     * Add usersInClassesAndCategories
     *
     * @param \Babesk\ORM\UserInClassAndCategory $usersInClassesAndCategories
     * @return SystemUsers
     */
    public function addUsersInClassesAndCategory(\Babesk\ORM\UserInClassAndCategory $usersInClassesAndCategories)
    {
        $this->usersInClassesAndCategories[] = $usersInClassesAndCategories;

        return $this;
    }

    /**
     * Remove usersInClassesAndCategories
     *
     * @param \Babesk\ORM\UserInClassAndCategory $usersInClassesAndCategories
     */
    public function removeUsersInClassesAndCategory(\Babesk\ORM\UserInClassAndCategory $usersInClassesAndCategories)
    {
        $this->usersInClassesAndCategories->removeElement($usersInClassesAndCategories);
    }

    /**
     * Get usersInClassesAndCategories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsersInClassesAndCategories()
    {
        return $this->usersInClassesAndCategories;
    }

    /**
     * Add cards
     *
     * @param \Babesk\ORM\BabeskCards $cards
     * @return SystemUsers
     */
    public function addCard(\Babesk\ORM\BabeskCards $cards)
    {
        $this->cards[] = $cards;

        return $this;
    }

    /**
     * Remove cards
     *
     * @param \Babesk\ORM\BabeskCards $cards
     */
    public function removeCard(\Babesk\ORM\BabeskCards $cards)
    {
        $this->cards->removeElement($cards);
    }

    /**
     * Get cards
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * Add elawaMeetingsVisiting
     *
     * @param \Babesk\ORM\ElawaMeeting $elawaMeetingsVisiting
     * @return SystemUsers
     */
    public function addElawaMeetingsVisiting(\Babesk\ORM\ElawaMeeting $elawaMeetingsVisiting)
    {
        $this->elawaMeetingsVisiting[] = $elawaMeetingsVisiting;

        return $this;
    }

    /**
     * Remove elawaMeetingsVisiting
     *
     * @param \Babesk\ORM\ElawaMeeting $elawaMeetingsVisiting
     */
    public function removeElawaMeetingsVisiting(\Babesk\ORM\ElawaMeeting $elawaMeetingsVisiting)
    {
        $this->elawaMeetingsVisiting->removeElement($elawaMeetingsVisiting);
    }

    /**
     * Get elawaMeetingsVisiting
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getElawaMeetingsVisiting()
    {
        return $this->elawaMeetingsVisiting;
    }

    /**
     * Add elawaMeetingsHosting
     *
     * @param \Babesk\ORM\ElawaMeeting $elawaMeetingsHosting
     * @return SystemUsers
     */
    public function addElawaMeetingsHosting(\Babesk\ORM\ElawaMeeting $elawaMeetingsHosting)
    {
        $this->elawaMeetingsHosting[] = $elawaMeetingsHosting;

        return $this;
    }

    /**
     * Remove elawaMeetingsHosting
     *
     * @param \Babesk\ORM\ElawaMeeting $elawaMeetingsHosting
     */
    public function removeElawaMeetingsHosting(\Babesk\ORM\ElawaMeeting $elawaMeetingsHosting)
    {
        $this->elawaMeetingsHosting->removeElement($elawaMeetingsHosting);
    }

    /**
     * Get elawaMeetingsHosting
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getElawaMeetingsHosting()
    {
        return $this->elawaMeetingsHosting;
    }

    /**
     * Add elawaDefaultMeetingRooms
     *
     * @param \Babesk\ORM\ElawaDefaultMeetingRoom $elawaDefaultMeetingRooms
     * @return SystemUsers
     */
    public function addElawaDefaultMeetingRoom(\Babesk\ORM\ElawaDefaultMeetingRoom $elawaDefaultMeetingRooms)
    {
        $this->elawaDefaultMeetingRooms[] = $elawaDefaultMeetingRooms;

        return $this;
    }

    /**
     * Remove elawaDefaultMeetingRooms
     *
     * @param \Babesk\ORM\ElawaDefaultMeetingRoom $elawaDefaultMeetingRooms
     */
    public function removeElawaDefaultMeetingRoom(\Babesk\ORM\ElawaDefaultMeetingRoom $elawaDefaultMeetingRooms)
    {
        $this->elawaDefaultMeetingRooms->removeElement($elawaDefaultMeetingRooms);
    }

    /**
     * Get elawaDefaultMeetingRooms
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getElawaDefaultMeetingRooms()
    {
        return $this->elawaDefaultMeetingRooms;
    }

    /**
     * Add booksToLend
     *
     * @param \Babesk\ORM\SchbasUserShouldLendBook $booksToLend
     * @return SystemUsers
     */
    public function addBooksToLend(\Babesk\ORM\SchbasUserShouldLendBook $booksToLend)
    {
        $this->booksToLend[] = $booksToLend;

        return $this;
    }

    /**
     * Remove booksToLend
     *
     * @param \Babesk\ORM\SchbasUserShouldLendBook $booksToLend
     */
    public function removeBooksToLend(\Babesk\ORM\SchbasUserShouldLendBook $booksToLend)
    {
        $this->booksToLend->removeElement($booksToLend);
    }

    /**
     * Get booksToLend
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBooksToLend()
    {
        return $this->booksToLend;
    }

    /**
     * Add selfpayingBookEntities
     *
     * @param \Babesk\ORM\SchbasSelfpayer $selfpayingBookEntities
     * @return SystemUsers
     */
    public function addSelfpayingBookEntity(\Babesk\ORM\SchbasSelfpayer $selfpayingBookEntities)
    {
        $this->selfpayingBookEntities[] = $selfpayingBookEntities;

        return $this;
    }

    /**
     * Remove selfpayingBookEntities
     *
     * @param \Babesk\ORM\SchbasSelfpayer $selfpayingBookEntities
     */
    public function removeSelfpayingBookEntity(\Babesk\ORM\SchbasSelfpayer $selfpayingBookEntities)
    {
        $this->selfpayingBookEntities->removeElement($selfpayingBookEntities);
    }

    /**
     * Get selfpayingBookEntities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSelfpayingBookEntities()
    {
        return $this->selfpayingBookEntities;
    }

    /**
     * Add schbasAccounting
     *
     * @param \Babesk\ORM\SchbasAccounting $schbasAccounting
     * @return SystemUsers
     */
    public function addSchbasAccounting(\Babesk\ORM\SchbasAccounting $schbasAccounting)
    {
        $this->schbasAccounting[] = $schbasAccounting;

        return $this;
    }

    /**
     * Remove schbasAccounting
     *
     * @param \Babesk\ORM\SchbasAccounting $schbasAccounting
     */
    public function removeSchbasAccounting(\Babesk\ORM\SchbasAccounting $schbasAccounting)
    {
        $this->schbasAccounting->removeElement($schbasAccounting);
    }

    /**
     * Get schbasAccounting
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSchbasAccounting()
    {
        return $this->schbasAccounting;
    }

    /**
     * Set priceGroup
     *
     * @param \Babesk\ORM\BabeskPriceGroups $priceGroup
     * @return SystemUsers
     */
    public function setPriceGroup(\Babesk\ORM\BabeskPriceGroups $priceGroup = null)
    {
        $this->priceGroup = $priceGroup;

        return $this;
    }

    /**
     * Get priceGroup
     *
     * @return \Babesk\ORM\BabeskPriceGroups 
     */
    public function getPriceGroup()
    {
        return $this->priceGroup;
    }

    /**
     * Add groups
     *
     * @param \Babesk\ORM\SystemGroups $groups
     * @return SystemUsers
     */
    public function addGroup(\Babesk\ORM\SystemGroups $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Babesk\ORM\SystemGroups $groups
     */
    public function removeGroup(\Babesk\ORM\SystemGroups $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add bookLending
     *
     * @param \Babesk\ORM\SchbasInventory $bookLending
     * @return SystemUsers
     */
    public function addBookLending(\Babesk\ORM\SchbasInventory $bookLending)
    {
        $this->bookLending[] = $bookLending;

        return $this;
    }

    /**
     * Remove bookLending
     *
     * @param \Babesk\ORM\SchbasInventory $bookLending
     */
    public function removeBookLending(\Babesk\ORM\SchbasInventory $bookLending)
    {
        $this->bookLending->removeElement($bookLending);
    }

    /**
     * Get bookLending
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBookLending()
    {
        return $this->bookLending;
    }

    /**
     * Add selfpayingBooks
     *
     * @param \Babesk\ORM\SchbasBook $selfpayingBooks
     * @return SystemUsers
     */
    public function addSelfpayingBook(\Babesk\ORM\SchbasBook $selfpayingBooks)
    {
        $this->selfpayingBooks[] = $selfpayingBooks;

        return $this;
    }

    /**
     * Remove selfpayingBooks
     *
     * @param \Babesk\ORM\SchbasBook $selfpayingBooks
     */
    public function removeSelfpayingBook(\Babesk\ORM\SchbasBook $selfpayingBooks)
    {
        $this->selfpayingBooks->removeElement($selfpayingBooks);
    }

    /**
     * Get selfpayingBooks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSelfpayingBooks()
    {
        return $this->selfpayingBooks;
    }
}
