<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/WebInterface.php';

class SettingsMainMenu extends Module {
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}

	public function execute ($dataContainer) {
		global $smarty;
		$smarty->display ($this->_smartyPath . 'mainMenu.tpl');
	}

	protected $_smartyPath;
}

?>