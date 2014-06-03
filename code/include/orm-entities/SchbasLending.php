<?php

namespace Babesk\ORM;
use Doctrine\ORM\Mapping;
use Doctrine\Common\Collections\ArrayCollection;

require_once PATH_INCLUDE . '/orm-entities/SystemUsers.php';
require_once PATH_INCLUDE . '/orm-entities/SchbasInventory.php';

/**
 * @Entity
 */
class SchbasLending {

	/**
	 * @Id
	 * @ManyToOne(targetEntity="SystemUsers")
	 * @JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * @Id
	 * @ManyToOne(targetEntity="SchbasInventory")
	 * @JoinColumn(name="inventory_id", referencedColumnName="id")
	 */
	protected $inventory;

	/**
	 * @Id
	 * @Column(type="datetime", name="lend_date")
	 */
	protected $lendDate;

	public function getUser() {
		return $this->user;
	}

	public function setUser($user) {
		$this->user = $user;
		return $this;
	}

	public function getInventory() {
		return $this->inventory;
	}

	public function setInventory($inventory) {
		$this->inventory = $inventory;
		return $this;
	}

	public function getLendDate() {
		return $this->lendDate;
	}

	public function setLendDate($lendDate) {
		$this->lendDate = $lendDate;
		return $this;
	}
}

?>