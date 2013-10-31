<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/WebInterface.php';
require_once PATH_WEB . '/headmod_Settings/Settings.php';

class SettingsMainMenu extends Settings {
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}

	public function execute ($dataContainer) {

		$smarty = $dataContainer->getSmarty();
		$smarty->display ($this->_smartyPath . 'mainMenu.tpl');
	}

	protected $_smartyPath;
}

?>
