<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 */
class SystemSchoolyears {

	/**
	 * @ID
	 * @Column(type="integer", name="ID")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @Column(type="string")
	 */
	protected $label;

	/**
	 * @Column(type="boolean")
	 */
	protected $active;

	/**
	 * @OneToMany(
	 *     targetEntity = "SystemUsersInGradesAndSchoolyears",
	 *     mappedBy = "schoolyear"
	 * )
	 */
	protected $usersInGradesAndSchoolyears;

	public function getId() {
		return $this->id;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
		return $this;
	}

	public function getActive() {
		return $this->active;
	}

	public function setActive($active) {
		$this->active = $active;
		return $this;
	}

}

?>