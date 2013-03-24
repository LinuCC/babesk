<?php

require_once PATH_INCLUDE . '/Module.php';

class MessageAdmin extends Module {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct ($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				default:
					die('Wrong action-value');
					break;
			}
		}
		else {
			$this->mainMenu();
		}
	}


	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		$this->_dataContainer = $dataContainer;
		$this->_interface = new MessageTemplateInterface($this->relPath,
			$this->_dataContainer->getSmarty());
	}

	protected function mainMenu() {

	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_dataContainer;

	protected $_interface;

}

?>