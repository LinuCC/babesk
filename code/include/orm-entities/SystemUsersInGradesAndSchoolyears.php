<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping;
use Doctrine\Common\Collections\ArrayCollection;

require_once PATH_INCLUDE . '/orm-entities/SystemUsers.php';
require_once PATH_INCLUDE . '/orm-entities/SystemGrades.php';
require_once PATH_INCLUDE . '/orm-entities/SystemSchoolyears.php';

/**
 * @Entity
 */
class SystemUsersInGradesAndSchoolyears {

	/**
	 * @Id
	 * @ManyToOne(
	 *     targetEntity = "SystemUsers",
	 *     inversedBy = "usersInGradesAndSchoolyears"
	 * )
	 * @JoinColumn(
	 *     name = "userId",
	 *     referencedColumnName = "ID",
	 *     nullable = false
	 * )
	 */
	protected $user;

	/**
	 * @Id
	 * @ManyToOne(
	 *     targetEntity = "SystemGrades",
	 *     inversedBy = "usersInGradesAndSchoolyears"
	 * )
	 * @JoinColumn(
	 *     name = "gradeId",
	 *     referencedColumnName = "ID",
	 *     nullable = false
	 * )
	 */
	protected $grade;

	/**
	 * @Id
	 * @ManyToOne(
	 *     targetEntity = "SystemSchoolyears",
	 *     inversedBy = "usersInGradesAndSchoolyears"
	 * )
	 * @JoinColumn(
	 *     name = "schoolyearId",
	 *     referencedColumnName = "ID",
	 *     nullable = false
	 * )
	 */
	protected $schoolyear;

	public function getUser() {
		return $this->user;
	}

	public function setUser($user) {
		$this->user = $user;
		return $this;
	}

	public function getGrade() {
		return $this->grade;
	}

	public function setGrade($grade) {
		$this->grade = $grade;
		return $this;
	}

	public function getSchoolyear() {
		return $this->schoolyear;
	}

	public function setSchoolyear($schoolyear) {
		$this->schoolyear = $schoolyear;
		return $this;
	}
}

?>