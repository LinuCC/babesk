<?php

namespace Babesk\ORM;

/**
 * @Entity
 */
class SystemSchoolSubjects {

	/**
	 * @id
	 * @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @Column(type="string")
	 */
	protected $abbreviation;

	/**
   * @Column(type="string")
	 */
	protected $name;

	public function getId() {
		return $this->id;
	}

	public function getAbbreviation() {
		return $this->abbreviation;
	}

	public function setAbbreviation($abbreviation) {
		$this->abbreviation = $abbreviation;
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

}

?>