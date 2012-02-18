<?php
//No direct access
defined('_WEXEC') or die("Access denied");
global $smarty;

require_once PATH_INCLUDE . '/global_settings_access.php';

$gsManager = new globalSettingsManager();
try {
	$help_str = $gsManager->getHelpText();
} catch (Exception $e) {
	die('Ein Fehler ist aufgetreten:'.$e->getMessage());
}

$smarty->assign('help_str', $help_str);
$smarty->display("web/modules/mod_help/help.tpl");

?>