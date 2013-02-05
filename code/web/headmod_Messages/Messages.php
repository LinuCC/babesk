<?php

require_once PATH_INCLUDE . '/HeadModule.php';

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
	public function __construct($name, $display_name) {
		parent::__construct($name, $display_name);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($moduleManager, $dataContainer) {
		$moduleManager->execute("Messages|MAdmin", false);
	}
}
?>