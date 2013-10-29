<?php

require_once PATH_INCLUDE . '/Module.php';

/**
 * class for Interface web
 * @author Mirek Hancl
 *
 */
class Pvau extends Module {

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

		$defaultMod = new ModuleExecutionCommand('root/web/Pvau/Pvp');
		$dataContainer->getAcl()->moduleExecute($defaultMod,
				$dataContainer);
	}
}
?>
