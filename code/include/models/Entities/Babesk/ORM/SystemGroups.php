<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * SystemGroups
 */
class SystemGroups
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
     * @var integer
     */
    private $lft;

    /**
     * @var integer
     */
    private $rgt;


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
     * @return SystemGroups
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
     * Set lft
     *
     * @param integer $lft
     * @return SystemGroups
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer 
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     * @return SystemGroups
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer 
     */
    public function getRgt()
    {
        return $this->rgt;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $targetEntity;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $mappedBy;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->targetEntity = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mappedBy = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add targetEntity
     *
     * @param \Babesk\ORM\B $targetEntity
     * @return SystemGroups
     */
    public function addTargetEntity(\Babesk\ORM\B $targetEntity)
    {
        $this->targetEntity[] = $targetEntity;

        return $this;
    }

    /**
     * Remove targetEntity
     *
     * @param \Babesk\ORM\B $targetEntity
     */
    public function removeTargetEntity(\Babesk\ORM\B $targetEntity)
    {
        $this->targetEntity->removeElement($targetEntity);
    }

    /**
     * Get targetEntity
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTargetEntity()
    {
        return $this->targetEntity;
    }

    /**
     * Add mappedBy
     *
     * @param \Babesk\ORM\g $mappedBy
     * @return SystemGroups
     */
    public function addMappedBy(\Babesk\ORM\g $mappedBy)
    {
        $this->mappedBy[] = $mappedBy;

        return $this;
    }

    /**
     * Remove mappedBy
     *
     * @param \Babesk\ORM\g $mappedBy
     */
    public function removeMappedBy(\Babesk\ORM\g $mappedBy)
    {
        $this->mappedBy->removeElement($mappedBy);
    }

    /**
     * Get mappedBy
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMappedBy()
    {
        return $this->mappedBy;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $users;


    /**
     * Add users
     *
     * @param \Babesk\ORM\SystemUsers $users
     * @return SystemGroups
     */
    public function addUser(\Babesk\ORM\SystemUsers $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Babesk\ORM\SystemUsers $users
     */
    public function removeUser(\Babesk\ORM\SystemUsers $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
}
