<?php

namespace administrator\Schbas\BookAssignments;

require_once PATH_ADMIN . '/Schbas/Schbas.php';

class BookAssignments extends \Schbas {

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
		$defaultMod = new \ModuleExecutionCommand(
			'root/administrator/Schbas/Dashboard'
		);
		$dataContainer->getAcl()->moduleExecute($defaultMod,
			$dataContainer);
	}
}
?>