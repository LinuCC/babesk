<?php
/**
 *@file menu.php Module to display the meals for this week
 * SPECIAL: This Module can be used without registration via the direct link (administrator/modules/mod_menu/menu.php)
 *@note the date has to be a date, NOT with hours, minutes or seconds, it will break some functions!
 */

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/Babesk/Babesk.php';

class Menu extends Babesk {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {
		$from_modul = defined('PATH_INCLUDE') or require_once("../../../include/path.php");
		require_once PATH_SMARTY . "/smarty_init.php";
		require_once 'AdminMenuProcessing.php';
		require_once 'AdminMenuInterface.php';

		$menuInterface = new AdminMenuInterface($from_modul, $this->relPath);
		$menuProcessing = new AdminMenuProcessing($from_modul, $menuInterface);

		$menuProcessing->ShowMenu();
	}
}
?>
