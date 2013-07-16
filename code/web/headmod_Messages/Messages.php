<?php

require_once PATH_INCLUDE . '/HeadModule.php';
require_once PATH_INCLUDE . '/ModuleExecutionInputParser.php';

/**
 * class for Interface web
 * @author Mirek Hancl
 *
 */
class Messages extends HeadModule {

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

		$defaultMod = new ModuleExecutionInputParser(
			'root/web/Messages/MessageMainMenu');
		$dataContainer->getAcl()->moduleExecute(
			$defaultMod, $dataContainer);
	}
}
?>
