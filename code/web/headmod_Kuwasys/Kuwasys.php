<?php

require_once PATH_INCLUDE . '/HeadModule.php';

/**
 * class for Interface web
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class Kuwasys extends HeadModule {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name) {
		
		parent::__construct($name, $display_name);
		defined('PATH_ACCESS_KUWASYS') or define('PATH_ACCESS_KUWASYS', PATH_ACCESS . '/headmod_Kuwasys');
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($moduleManager, $dataContainer) {
		$moduleManager->execute("Kuwasys|MainMenu", false);
	}
}

?>