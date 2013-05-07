<?php

require_once PATH_INCLUDE . '/Module.php';

class SchbasSettings extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		require_once 'AdminSchbasSettingsInterface.php';
		require_once 'AdminSchbasSettingsProcessing.php';

		$SchbasSettingsInterface = new AdminSchbasSettingsInterface($this->relPath);
		$SchbasSettingsProcessing = new AdminSchbasSettingsProcessing($SchbasSettingsInterface);

		$SchbasSettingsInterface->InitialMenu();
		
	}
}

?>