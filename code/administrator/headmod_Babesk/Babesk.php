<?php

require_once PATH_INCLUDE . '/HeadModule.php';

/**
 * class for Interface administrator
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class Babesk extends HeadModule {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name) {
		parent::__construct($name, $display_name);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute() {
		//function not needed, javascript is doing everything
	}
	
	public function executeModule($mod_name, $dataContainer) {
		parent::executeModule($mod_name, $dataContainer);
	}
}
?>