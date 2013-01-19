<?php

/**
 * Represents a Class with active Users
 */
class CctClass {

	public function __construct ($id, $label) {
		$this->setId ($id);
		$this->setLabel ($label);
	}

	/**
	 * Searches for an item with the ID $classId in $haystack
	 *
	 * @param Integer $classId the ClassId to search for
	 * @param array(CctClass) $haystack the container of classes to search the classId in
	 * @return CctClass the class if found, false if not found
	 */
	public static function hasClassById ($classId, $haystack) {
		foreach ($haystack as $c) {
			if ($c->getId () == $classId) {
				return $c;
			}
		}
		return false;
	}

	/**
	 * Returns the classes ID
	 *
	 * @return integer the ID of the Class of this instance
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * Sets the ID of this class
	 *
	 * @param Integer $id The ID of the Class of this instance
	 */
	public function setId($id) {
		$this->_id = $id;
		return $this;
	}


	/**
	 * Returns the Label of this Class-Instance
	 *
	 * @return string The Label of this Class
	 */
	public function getLabel() {
		return $this->_label;
	}

	/**
	 * Sets the label of this class
	 *
	 * @param String $label The Label of this Class
	 */
	public function setLabel($label) {
		$this->_label = $label;
		return $this;
	}


	/**
	 * Returns all of the users that are in this class
	 *
	 * @return array (array()) The Users in this class
	 */
	public function getUsers() {
		return $this->_users;
	}

	/**
	 * Sets all of the Users in this class
	 *
	 * @param array (array()) $users The Users in this class
	 */
	public function setUsers($users) {
		$this->_users = $users;
		return $this;
	}

	/**
	 * Adds an user to this class
	 *
	 * @param array () $user An array of the users data
	 */
	public function addUser ($user) {
		$this->_users [] = $user;
	}


	/**
	 * Returns the Classteacher of this class
	 *
	 * @return array () The Classteacher of this class
	 */
	public function getClassteachers() {
		return $this->_classteachers;
	}

	/**
	 * Sets the Classteacher of this class
	 *
	 * @param Array  $classteacher The Classteacher of this class
	 */
	public function setClassteachers($classteachers) {
		$this->_classteachers = $classteachers;
		return $this;
	}

	/**
	 * Adds an Classteacher to the classteachers of this class
	 */
	public function addClassteacher ($classteacherFullname) {
		$this->_classteachers [] = $classteacherFullname;
	}


	/**
	 * Returns the unitName of this class
	 *
	 * @return string The name of the unit of this class
	 */
	public function getUnitName() {
		return $this->_unitName;
	}

	/**
	 * Sets the unitname of this class
	 *
	 * @param string $unitName The name of the unit of this class
	 */
	public function setUnitName($unitName) {
		$this->_unitName = $unitName;
		return $this;
	}

	public function hasUnitName () {
		if ($this->_unitName !== NULL) {
			return true;
		}
		return false;
	}

	/**
	 * Checks if this class has an Classteacher
	 */
	public function hasClassteacher ($classteacherFullname) {
		if ($this->_classteachers !== NULL) {
			foreach ($this->_classteachers as $classteacher) {
				if ($classteacher == $classteacherFullname) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Returns a string that lists all of the Classteachers
	 */
	public function getClassteacherString () {
		$str = '';
		if ($this->_classteachers !== NULL) {
			foreach ($this->_classteachers as $ctName) {
				$str .= sprintf ('%s, ', $ctName);
			}
			$str = rtrim ($str, ', ');
		}
		else {
			//leave space so that you can write the classteacher
			$str = '                                      ';
		}
		return $str;
	}

	public function hasUser ($userId) {
		foreach ($this->_users as $user) {
			if ($user ['userId'] == $userId) {
				return true;
			}
		}
		return false;
	}

	protected $_users;
	protected $_classteachers;
	protected $_label;
	protected $_id;
	protected $_unitName;

}

?>