<?php

require_once PATH_INCLUDE . '/Module.php';

/**
 * class for Interface web
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class Kuwasys extends Module {

	///////////////////////////////////////////////////////////////////////
	//Constructor
	///////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name,$headmod_menu) {

		parent::__construct($name, $display_name,$headmod_menu);
	}

	///////////////////////////////////////////////////////////////////////
	//Methods
	///////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$defaultMod = new ModuleExecutionCommand('root/web/Kuwasys/MainMenu');
		$dataContainer->getAcl()->moduleExecute($defaultMod, $dataContainer);
	}

	///////////////////////////////////////////////////////////////////////
	//Attributes
	///////////////////////////////////////////////////////////////////////

	public static $buttonBackToMM = '<br>
		<form action="index.php?section=Kuwasys|MainMenu" method="post">
		<input type="submit" value="Zum Hauptmenü"></form>';


}

?>
