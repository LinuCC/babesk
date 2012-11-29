<?php

require_once PATH_INCLUDE . '/HeadModule.php';

/**
 * class for Interface web
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class Settings extends HeadModule {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name) {
		parent::__construct($name, $display_name);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($moduleManager, $dataContainer) {
		echo '<b>Das hier noch verändern!!!!!!</b>';
		$moduleManager->execute("Settings|ChangePresetPassword", false);
	}
}

?>