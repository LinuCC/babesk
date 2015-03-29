<?php

require_once PATH_INCLUDE . '/Module.php';

/**
 * class for Interface web
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class Settings extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name,$headmod_menu) {
		parent::__construct($name, $display_name,$headmod_menu);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {

		$defaultMod = new ModuleExecutionCommand(
			'root/web/Settings/SettingsMainMenu');
		$dataContainer->getAcl()->moduleExecute($defaultMod,
				$dataContainer);

	}
}

?>
