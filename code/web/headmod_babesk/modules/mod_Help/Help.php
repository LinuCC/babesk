<?php

require_once PATH_INCLUDE . '/Module.php';

class Help extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute() {
		//No direct access
		defined('_WEXEC') or die("Access denied");
		global $smarty;
		
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';
		
		$gsManager = new GlobalSettingsManager();
		try {
			$help_str = $gsManager->getHelpText();
		} catch (Exception $e) {
			die('Ein Fehler ist aufgetreten:'.$e->getMessage());
		}
		
		$smarty->assign('help_str', $help_str);
		$smarty->display("web/modules/mod_help/help.tpl");
	}
}
?>