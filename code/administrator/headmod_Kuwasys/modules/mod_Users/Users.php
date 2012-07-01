<?php

require_once PATH_INCLUDE . '/Module.php';
require_once 'UsersInterface.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersManager.php';

class Users extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $_usersInterface;
	private $_usersManager;
	/**
	 * @var KuwasysLanguageManager
	 */
	private $_languageManager;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute ($dataContainer) {
		
		$this->entryPoint($dataContainer);
		
		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				default:
					$this->_usersInterface->dieError($this->_languageManager->getText('actionValueWrong'));
			}
			
		}
		else {
			$this->showMainMenu();
		}
		
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Implements
	public function entryPoint ($dataContainer) {
		
		defined('_AEXEC') or die('Access denied');
		$this->_usersManager = new KuwasysUsersManager();
		$this->_usersInterface = new UsersInterface($this->relPath);
		$this->_languageManager = $dataContainer->getLanguageManager();
		$this->_languageManager->setModule('Users');
	}
	
	public function showMainMenu () {
		
		$this->_usersInterface->showMainMenu();
	}
}
?>