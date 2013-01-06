<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersInClassStatusManager.php';

class MainMenu extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function execute ($dataContainer) {

		$this->entryPoint();
		$classes = $this->getAllClassesOfUser();
		$classes = $this->sortClassesByUnit ($classes);
		$this->displayMainMenu($classes);
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	private function entryPoint () {

		defined('_WEXEC') or die("Access denied");
		$this->initManagers();
		global $smarty;
		$this->_smarty = $smarty;
	}

	private function initManagers () {

		require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInClass.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysClassUnitManager.php';

		$this->_classManager = new KuwasysClassManager();
		$this->_userManager = new KuwasysUsersManager();
		$this->_jointUsersInClassManager = new KuwasysJointUsersInClass();
		$this->_usersInClassStatusManager = new KuwasysUsersInClassStatusManager ();
		$this->_classUnitManager = new KuwasysClassUnitManager ();
	}

	private function getAllClassesOfUser () {
		$classes = array();
		$jointsUsersInClass = $this->getAllJointsUsersInClassOfUser();
		if (isset($jointsUsersInClass)) {
			foreach ($jointsUsersInClass as $joint) {
				$class = $this->getClassFromDatabase($joint['ClassID']);
				$status = $this->usersInClassStatusGet ($joint['statusId']);
				$unit = $this->unitOfClassGet ($class ['unitId']);
				if ($status)
					$class['statusTranslated'] = $status ['translatedName'];
				if ($unit)
					{$class ['unit'] = $unit;}
				$classes[] = $class;
			}
			return $classes;
		}
	}

	protected function sortClassesByUnit ($classes) {
		if (!$classes)
			{return;}
		$sorted = array ();
		foreach ($classes as $class) {
			foreach ($sorted as $unit) {
				if ($unit->unit ['ID'] == $class ['unit'] ['ID']) {
					$unit->classes [] = $class;
					continue 2;
				}
			}
			$sorted [] = new SortedClassesByUnits ($class ['unit'], $class);
		}
		return $sorted;
	}

	private function usersInClassStatusGet ($statusId) {
		try {
			$status = $this->_usersInClassStatusManager->statusGet($statusId);
		} catch (MySQLVoidDataException $e) {
			return false;
		}
		return $status;
	}

	protected function unitOfClassGet ($unitId) {
		try {
			$unit = $this->_classUnitManager->unitGet ($unitId);
		} catch (Exception $e) {
			$this->dieErrorMsg ('Konnte bestimmten Kursen keine Tage zuordnen');
		}
		return $unit;
	}

	private function getAllJointsUsersInClassOfUser () {

		try {
			$joints = $this->_jointUsersInClassManager->getAllJointsOfUserId($_SESSION['uid']);
		} catch (MySQLVoidDataException $e) {
			$this->addErrorMsg('Es wurden noch keine Kurse ausgewählt.');
		}
		catch (Exception $e) {
			$this->dieErrorMsg('error fetching ClassLinks from the Database');
		}
		if(isset($joints)) {
			return $joints;
		}
	}

	private function getClassFromDatabase ($classId) {
		try {
			$class = $this->_classManager->getClass($classId);
		} catch (MySQLVoidDataException $e) {
			$this->addErrorMsg(
				'Ein Kurs ist seltsamerweise nicht mehr vorhanden, aber du bist dort immer noch angemeldet.');
		}
		catch (Exception $e) {
			$this->addErrorMsg('error fetching ClassData from the Database.');
		}
		if (isset($class)) {
			return $class;
		}
	}

	private function addErrorMsg ($str) {

		$this->_smarty->append('error', $str . '<br>');
	}

	private function dieErrorMsg ($str) {

		$this->_smarty->append('error', $str . '<br>');
		$this->displayMainMenu(NULL);
	}

	private function displayMainMenu ($classes) {

		$this->_smarty->assign('classes', $classes);
		$this->_smarty->display($this->_smartyPath . 'mainMenu.tpl');
	}
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	private $_classManager;
	private $_userManager;
	private $_jointUsersInClassManager;
	protected $_classUnitManager;
	private $_usersInClassStatusManager;
	private $_smarty;
	private $_smartyPath;
}

class SortedClassesByUnits {
	public function __construct ($unit, $class) {
		$this->unit = $unit;
		$this->classes [] = $class;
	}
	public $classes;
	public $unit;
}

?>