<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserInClassAndCategory
 */
class UserInClassAndCategory
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Babesk\ORM\SystemUsers
     */
    private $user;

    /**
     * @var \Babesk\ORM\KuwasysClasses
     */
    private $class;

    /**
     * @var \Babesk\ORM\UserInClassStatus
     */
    private $status;

    /**
     * @var \Babesk\ORM\KuwasysClassCategories
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
     * Set user
     *
     * @param \Babesk\ORM\SystemUsers $user
     * @return UserInClassAndCategory
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
     * Set class
     *
     * @param \Babesk\ORM\KuwasysClasses $class
     * @return UserInClassAndCategory
     */
    public function setClass(\Babesk\ORM\KuwasysClasses $class = null)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return \Babesk\ORM\KuwasysClasses 
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set status
     *
     * @param \Babesk\ORM\UserInClassStatus $status
     * @return UserInClassAndCategory
     */
    public function setStatus(\Babesk\ORM\UserInClassStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Babesk\ORM\UserInClassStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set category
     *
     * @param \Babesk\ORM\KuwasysClassCategories $category
     * @return UserInClassAndCategory
     */
    public function setCategory(\Babesk\ORM\KuwasysClassCategories $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Babesk\ORM\KuwasysClassCategories 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
