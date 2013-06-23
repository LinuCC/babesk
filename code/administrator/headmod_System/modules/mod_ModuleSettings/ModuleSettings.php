<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/AdminInterface.php';

class ModuleSettings extends Module {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * The entry-Point of the Module, when it gets executed
	 *
	 * @param  DataContainer $dataContainer a instance containing general data
	 */
	protected function entryPoint($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		$this->_smarty = $dataContainer->getSmarty();
		$this->_modulemanager = $dataContainer->getModulemanager();
		$this->_groupmanager = $dataContainer->getGroupmanager();
		$this->_interface = new AdminInterface($this->relPath,
			$this->_smarty);
	}

	protected function groupsAllGet() {

	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_modulemanager;
	protected $_groupmanager;
}

?>
