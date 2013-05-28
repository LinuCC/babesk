<?php

require_once PATH_INCLUDE . '/HeadModule.php';

/**
 * Processes Data from Javascript-Requests
 *
 * Offers several server-side functions designed to allow Client-side Javascript
 * access to these functions
 *
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 */
class JsDataProcessor extends HeadModule {

	////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////
	public function __construct($name, $display_name, $mod_menu) {
		parent::__construct($name, $display_name, $mod_menu);
	}

	////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////
	public function execute($moduleManager, $dataContainer) {
		$dataContainer->getInterface()->dieError('No direct Access to Headmod');
	}

	public function executeModule($mod_name, $dataContainer) {
		parent::executeModule($mod_name, $dataContainer);
	}
}
?>