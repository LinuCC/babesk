<?php
/**
 * stores the functions used in the whole admin-area
 * NEEDS: a smarty-object, the paths [path.php]
 */

/**
 * die_error shows an error to the user and then die()'s the process
 * accepts one or two parameter. If one, Smarty will inherit from the standard-file.
 */
function die_error($string, $inh_path = BASE_PATH) {
	global $smarty;
	$smarty->assign('inh_path', $inh_path);
	$smarty->assign('error', $string);
	$smarty->display(PATH_SMARTY.'/templates/administrator/message.tpl');
	die();
}

/**
 * die_msg shows a message to the user and then die()'s the process
 * accepts one or two parameter. If one, Smarty will inherit from the standard-file.
 * if two parameter are given, Smarty will inherit from the given string.
 * @param string The Path to the template-file for Smarty to inherit from
 */
function die_msg($string, $inh_path = BASE_PATH) {
	global $smarty;
	$smarty->assign('inh_path', $inh_path);
	$smarty->assign('message', $string);
	$smarty->display(PATH_SMARTY.'/templates/administrator/message.tpl');
	die();
}
?>