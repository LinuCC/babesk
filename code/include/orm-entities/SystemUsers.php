<?php

namespace Babesk\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="SystemUsers")
 */
class SystemUsers {

	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @Column(type="string")
	 */
	protected $forename;

	/**
	 * @Column(type="integer")
	 */
	protected $GID;

	/**
	 * @ORM\ManyToMany(targetEntity="SystemGroups")
	 * @ORM\JoinTable(name="SystemUsersInGroups",
	 *     joinColumns= {@ORM\JoinColumn(
	 *         name="userId", referencedColumnName="ID"
	 *     )},
	 *     inverseJoinColumns={@ORM\JoinColumn(
	 *         name="groupId", referencedColumnName="ID"
	 *     )}
	 * ))
	 */
	protected $groups;

	public function getForename() {
		return $this->forename;
	}

	public function setForename($forename) {
		$this->forename = $forename;
		return $this;
	}

	public function getGid() {
		return $this->GID;
	}

	public function setGid($GID) {
		$this->GID = $GID;
		return $this;
	}

	public function __construct() {

		$this->groups = new ArrayCollection();
	}
}

?>