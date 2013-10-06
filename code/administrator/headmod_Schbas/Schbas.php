<?php

require_once PATH_INCLUDE . '/Module.php';

/**
 * class for Interface administrator
 * @author Mirek Hancl
 *
 */
class Schbas extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name,$headmod_menu) {
		parent::__construct($name, $display_name,$headmod_menu);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($moduleManager) {
		//function not needed, javascript is doing everything
	}

	public function executeModule($mod_name, $dataContainer) {
		parent::executeModule($mod_name, $dataContainer);
	}
}
?>
