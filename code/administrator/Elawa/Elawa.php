<?php

namespace administrator\Elawa;

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/exception_def.php';

class Elawa extends \Module {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct ($name, $display_name,$headmod_menu) {

		parent::__construct($name, $display_name,$headmod_menu);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute ($dataContainer) {
		//function not needed, javascript is doing everything
	}

}

?>