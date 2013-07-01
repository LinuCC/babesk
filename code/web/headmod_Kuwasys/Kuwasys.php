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
	public static $buttonBackToMM = '<br>
		<form action="index.php?section=Kuwasys|MainMenu" method="post">
		<input type="submit" value="Zum Hauptmenü"></form>';

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name,$headmod_menu) {

		parent::__construct($name, $display_name,$headmod_menu);
		defined('PATH_ACCESS_KUWASYS') or define('PATH_ACCESS_KUWASYS', PATH_ACCESS . '/headmod_Kuwasys');
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {
		$dataContainer->getAcl()->moduleExecute('root/web/Kuwasys/MainMenu',
			$dataContainer);
		// $moduleManager->execute("Kuwasys|MainMenu", false);
	}
}

?>
