<?php

require_once PATH_INCLUDE . '/Module.php';
require_once 'UsersInterface.php';

class Users extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $_usersInterface;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute () {
		
		$this->entryPoint();
		
		$this->_usersInterface->dieMsg('Hallu!');
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Implements
	public function entryPoint () {
		
		defined('_AEXEC') or die('Access denied');
		$this->_usersInterface = new UsersInterface($this->relPath);
	}
}
?>