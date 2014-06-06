<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping;
use Doctrine\Common\Collections\ArrayCollection;

require_once PATH_INCLUDE . '/orm-entities/SystemSchooltypes.php';

/**
 * @Entity
 */
class SystemGrades {

	/**
	 * @Id
	 * @Column(name="ID", type="integer")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @Column(type="string")
	 */
	protected $label;

	/**
	 * @Column(type="integer")
	 */
	protected $gradelevel;

	/**
	 * @ManyToOne(targetEntity="SystemSchooltypes")
	 * @JoinColumn(name="schooltypeId", referencedColumnName="ID")
	 */
	protected $schooltype;

	/**
	 * @OneToMany(
	 *     targetEntity = "SystemUsersInGradesAndSchoolyears",
	 *     mappedBy = "grade"
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

	public function getGradelevel() {
		return $this->gradelevel;
	}

	public function setGradelevel($gradelevel) {
		$this->gradelevel = $gradelevel;
		return $this;
	}

	public function getSchooltype() {
		return $this->schooltype;
	}

	public function setSchooltype($schooltype) {
		$this->schooltype = $schooltype;
		return $this;
	}
}

?>