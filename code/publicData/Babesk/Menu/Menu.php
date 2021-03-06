<?php

require_once PATH_INCLUDE . '/Module.php';

class Menu extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct($name, $display_name, $path) {
		require_once 'AdminMenuProcessing.php';
		require_once 'AdminMenuInterface.php';

		parent::__construct($name, $display_name, $path);

		$this->smartyPath = PATH_SMARTY_TPL . '/web' . $path;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {
		$menuInterface = new AdminMenuInterface($dataContainer->getSmarty(), $this->relPath);
		$menuProcessing = new AdminMenuProcessing($menuInterface);
		$menuProcessing->ShowMenu();
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_smartyPath;
}


?>