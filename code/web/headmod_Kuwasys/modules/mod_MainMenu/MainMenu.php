<?php

require_once PATH_INCLUDE . '/Module.php';

class MainMenu extends Module {
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct($name, $display_name, $path) {
		
		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function execute () {
		
		$this->entryPoint();
		$classes = $this->getAllClassesOfUser();
		$this->displayMainMenu($classes);
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	private function entryPoint () {
		
		defined('_WEXEC') or die("Access denied");
		$this->initManagers();
	}
	
	private function initManagers () {
		
		require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInClass.php';
		
		$this->_classManager = new KuwasysClassManager();
		$this->_userManager = new KuwasysUsersManager();
		$this->_jointUsersInClassManager = new KuwasysJointUsersInClass();
	}
	
	private function getAllClassesOfUser () {
		
		$classes = array();
		$jointsUsersInClass = $this->getAllJointsUsersInClassOfUser();
		foreach ($jointsUsersInClass as $joint) {
			$class = $this->getClassFromDatabase($joint ['ClassID']);
			$class ['status'] = $joint ['status'];
			$classes [] = $class;
		}
		return $classes;
	}
	
	private function getAllJointsUsersInClassOfUser () {
		
		try {
			$joints = $this->_jointUsersInClassManager->getAllJointsOfUserId($_SESSION['uid']);
		} catch (MySQLVoidDataException $e) {
			$this->addErrorMsg('Es wurden noch keine Module ausgewählt.');
		} catch (Exception $e) {
			$this->dieErrorMsg('error fetching ClassLinks from the Database');
		}
		return $joints;
	}
	
	private function getClassFromDatabase ($classId) {
		
		try {
			$class = $this->_classManager->getClass($classId);
		} catch (MySQLVoidDataException $e) {
			$this->addErrorMsg('Ein Modul ist seltsamerweise nicht mehr vorhanden, aber du bist dort immer noch angemeldet.');
		} catch (Exception $e) {
			$this->addErrorMsg('error fetching ClassData from the Database.');
		}
		return $class;
	}
	
	private function addErrorMsg ($str) {
		
		$this->_smarty->append('error', $str . '<br>');
	}
	
	private function dieErrorMsg ($str) {
		
		$this->_smarty->append('error', $str . '<br>');
		$this->displayMainMenu(NULL);
	}
	
	private function displayMainMenu ($classes) {
		
		global $smarty;
		$this->_smarty = $smarty;
		$this->_smarty->assign('classes', $classes);
		$this->_smarty->display($this->_smartyPath . 'mainMenu.tpl');
	}
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	
	private $_classManager;
	private $_userManager;
	private $_jointUsersInClassManager;
	private $_smarty;
	private $_smartyPath;
}

?>