<?php

require_once PATH_INCLUDE . '/HeadModule.php';

/**
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 */
class Babesk extends HeadModule {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $mod_menu) {
		parent::__construct($name, $display_name, $mod_menu);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($moduleManager, $dataContainer) {
		$dataContainer->getInterface()->dieError ('No direct Access to Babesk');
	}

	public function executeModule($mod_name, $dataContainer) {
		parent::executeModule($mod_name, $dataContainer);
	}
}
?>