<?php

require_once PATH_INCLUDE . '/Module.php';

/**
 * class for Interface administrator
 */
class System extends Module {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name,$headmod_menu) {
		parent::__construct($name, $display_name,$headmod_menu);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {
		//function not needed, javascript is doing everything
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function isKuwasysActivated() {
		return (boolean) $this->_acl->moduleGet('root/administrator/Kuwasys');
	}

	protected function isBabeskActivated() {
		return (boolean) $this->_acl->moduleGet('root/administrator/Babesk');
	}
}
?>
