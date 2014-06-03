<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping;

/**
 * @Entity
 */
class SystemGroups {

	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @Column(type="string")
	 */
	protected $name;

	/**
	 * @Column(type="integer")
	 */
	protected $lft;

	/**
	 * @Column(type="integer")
	 */
	protected $rgt;

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getLft() {
		return $this->lft;
	}

	public function setLft($lft) {
		$this->lft = $lft;
		return $this;
	}

	public function getRgt() {
		return $this->rgt;
	}

	public function setRgt($rgt) {
		$this->rgt = $rgt;
		return $this;
	}
}

?>