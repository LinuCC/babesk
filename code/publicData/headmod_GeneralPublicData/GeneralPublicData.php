<?php

require_once PATH_INCLUDE . '/Module.php';

/**
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 */
class GeneralPublicData extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $mod_menu) {
		parent::__construct($name, $display_name, $mod_menu);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($moduleManager) {
		$dataContainer->getInterface()->dieError ('No direct Access to Login');
	}

	public function executeModule($mod_name, $dataContainer) {
		parent::executeModule($mod_name, $dataContainer);
	}
}
?>
