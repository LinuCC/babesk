<?php

/**
 * Shows an error to the user. Makes sure that the site is correctly shown
 */
function show_error ($string) {
	global $smarty;
	$smarty->display('web/header.tpl');
	echo $string;
	$smarty->display('web/footer.tpl');
}

?>