<?php

namespace web\Kuwasys\ClassList;

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/headmod_Kuwasys/modules/mod_ClassList/ClassList.php';

class UserSelectionsApply extends \web\Kuwasys\ClassList {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructs the Module
	 * @param string $name         The Name of the Module
	 * @param string $display_name The Name that should be displayed to the
	 *                             User
	 * @param string $path         A relative Path to the Module
	 */
	public function __construct ($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Executes the Module, does things based on ExecutionRequest
	 *
	 * @param  DataContainer $dataContainer contains data needed by the Module
	 */
	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Initializes data needed by the Object
	 *
	 * @param  DataContainer $dataContainer Contains data needed by Classes
	 */
	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>