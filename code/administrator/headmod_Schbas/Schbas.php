<?php

require_once PATH_INCLUDE . '/HeadModule.php';

/**
 * class for Interface administrator
 * @author Mirek Hancl
 *
 */
class Schbas extends HeadModule {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name) {
		parent::__construct($name, $display_name);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($moduleManager, $dataContainer) {
		//function not needed, javascript is doing everything
	}

	public function executeModule($mod_name, $dataContainer) {
		parent::executeModule($mod_name, $dataContainer);
	}
}
?>