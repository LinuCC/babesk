<?php
/**
 *@file menu.php Module to display the meals for this week
 * SPECIAL: This Module can be used without registration via the direct link (administrator/modules/mod_menu/menu.php) 
 *@note the date has to be a date, NOT with hours, minutes or seconds, it will break some functions!
 */

$from_modul = defined('PATH_INCLUDE') or require_once("../../../include/path.php");
require_once PATH_SMARTY . "/smarty_init.php";
require_once 'AdminMenuProcessing.php';
require_once 'AdminMenuInterface.php';

$menuProcessing = new AdminMenuProcessing($from_modul);
// $menuInterface = new AdminMenuInterface($from_modul);

$menuProcessing->ShowMenu();

?>